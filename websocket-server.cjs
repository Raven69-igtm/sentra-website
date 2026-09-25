const { WebSocketServer } = require('ws');
const mysql = require('mysql');
require('dotenv').config();

const DB_CONFIG = {
    host: process.env.DB_HOST || '127.0.0.1',
    port: process.env.DB_PORT || 3306,
    user: process.env.DB_USERNAME || 'root',
    password: process.env.DB_PASSWORD || 'root',
    database: process.env.DB_DATABASE || 'sentra',
};

const WS_PORT = process.env.WS_PORT || 3021;
const WS_HOST = process.env.WS_HOST || '127.0.0.1';

// Create database connection pool
const db = mysql.createPool({
    ...DB_CONFIG,
    connectionLimit: 10,
    waitForConnections: true,
    queueLimit: 0
});

// Test database connection
db.getConnection((err, connection) => {
    if (err) {
        console.error('❌ Database connection failed:', err.message);
        process.exit(1);
    }
    console.log('✅ Database connected successfully');
    connection.release();
});

function query(sql, params = []) {
    return new Promise((resolve, reject) => {
        db.query(sql, params, (err, results) => {
            if (err) {
                console.error('Query error:', err.message);
                reject(err);
            } else {
                resolve(results);
            }
        });
    });
}

const clients = new Map();

// Create WebSocket server
const wss = new WebSocketServer({ 
    port: WS_PORT,
    host: WS_HOST,
    clientTracking: true,
    perMessageDeflate: false
});

wss.on('listening', () => {
    console.log(`🚀 WebSocket server running on ${WS_HOST}:${WS_PORT}`);
    console.log(`� Connect using: ws://localhost:${WS_PORT}`);
    console.log(`   For Flutter web in Chrome, use the same URL`);
});

wss.on('connection', (ws, req) => {
    const clientIp = req.socket.remoteAddress;
    console.log(`📲 New connection from ${clientIp}`);
    
    let userId = null;
    let userType = null;

    // Send welcome message
    ws.send(JSON.stringify({
        type: 'connected',
        message: 'WebSocket connection established',
        timestamp: new Date().toISOString()
    }));

    // Heartbeat mechanism to keep connection alive
    ws.isAlive = true;
    ws.on('pong', () => {
        ws.isAlive = true;
    });

    ws.on('message', async (raw) => {
        let data;
        try {
            data = JSON.parse(raw);
        } catch (e) {
            console.error('Invalid JSON received:', e.message);
            ws.send(JSON.stringify({
                type: 'error',
                message: 'Invalid JSON format'
            }));
            return;
        }

        try {
            switch (data.type) {
                case 'register':
                    userId = String(data.user_id);
                    userType = data.user_type || 'unknown';
                    clients.set(userId, ws);
                    console.log(`✅ User ${userId} registered (${userType})`);
                    
                    // Send confirmation
                    ws.send(JSON.stringify({
                        type: 'registered',
                        user_id: userId,
                        user_type: userType,
                        online_users: Array.from(clients.keys())
                    }));
                    
                    // Notify all clients about new user
                    broadcastToAll(JSON.stringify({
                        type: 'user_online',
                        user_id: userId
                    }), userId);
                    break;

                case 'send_message':
                    await handleSendMessage(data);
                    break;

                case 'edit_message':
                    await handleEditMessage(data);
                    break;

                case 'delete_message':
                    await handleDeleteMessage(data);
                    break;

                case 'typing':
                    handleTyping(data);
                    break;
                
                case 'ping':
                    ws.send(JSON.stringify({ type: 'pong' }));
                    break;

                default:
                    console.warn('Unknown message type:', data.type);
            }
        } catch (err) {
            console.error('Error handling message:', err.message);
            ws.send(JSON.stringify({
                type: 'error',
                message: 'Error processing message',
                details: err.message
            }));
        }
    });

    ws.on('close', () => {
        if (userId) {
            clients.delete(userId);
            console.log(`👋 User ${userId} disconnected`);
            
            // Notify all clients about user offline
            broadcastToAll(JSON.stringify({
                type: 'user_offline',
                user_id: userId
            }));
        }
    });

    ws.on('error', (err) => {
        console.error('WebSocket error:', err.message);
    });
});

// Heartbeat interval to detect dead connections
const heartbeatInterval = setInterval(() => {
    wss.clients.forEach((ws) => {
        if (ws.isAlive === false) {
            return ws.terminate();
        }
        ws.isAlive = false;
        ws.ping();
    });
}, 30000); // Check every 30 seconds

wss.on('close', () => {
    clearInterval(heartbeatInterval);
});

async function handleSendMessage(data) {
    const { sender_id, receiver_id, message, replied_to_id } = data;
    
    if (!sender_id || !receiver_id || !message) {
        console.error('Missing required fields for send_message');
        return;
    }

    try {
        const result = await query(
            'INSERT INTO chats (sender_id, receiver_id, message, replied_to_id, sent_at, is_read, is_notified) VALUES (?, ?, ?, ?, NOW(), 0, 0)',
            [sender_id, receiver_id, message, replied_to_id || null]
        );

        const [inserted] = await query('SELECT * FROM chats WHERE id_chat = ?', [result.insertId]);

        if (!inserted) {
            console.error('Failed to retrieve inserted message');
            return;
        }

        const payload = JSON.stringify({
            type: 'new_message',
            id_chat: inserted.id_chat,
            sender_id: inserted.sender_id,
            receiver_id: inserted.receiver_id,
            message: inserted.message,
            replied_to_id: inserted.replied_to_id,
            sent_at: inserted.sent_at,
            is_read: inserted.is_read,
            is_notified: inserted.is_notified,
        });

        sendTo(String(sender_id), payload);
        sendTo(String(receiver_id), payload);

        console.log(`💬 Message sent: ${sender_id} → ${receiver_id}`);
    } catch (err) {
        console.error('Error in handleSendMessage:', err.message);
        throw err;
    }
}

async function handleEditMessage(data) {
    const { id_chat, new_message } = data;
    
    if (!id_chat || !new_message) {
        console.error('Missing required fields for edit_message');
        return;
    }

    try {
        await query('UPDATE chats SET message = ? WHERE id_chat = ?', [new_message, id_chat]);
        const [updated] = await query('SELECT * FROM chats WHERE id_chat = ?', [id_chat]);
        
        if (!updated) {
            console.error('Message not found for edit');
            return;
        }

        const payload = JSON.stringify({
            type: 'message_edited',
            chat_id: updated.id_chat,
            new_message: updated.message,
            sent_at: updated.sent_at,
        });

        sendTo(String(updated.sender_id), payload);
        sendTo(String(updated.receiver_id), payload);
        
        console.log(`✏️ Message edited: ${id_chat}`);
    } catch (err) {
        console.error('Error in handleEditMessage:', err.message);
        throw err;
    }
}

async function handleDeleteMessage(data) {
    const { id_chat } = data;
    
    if (!id_chat) {
        console.error('Missing id_chat for delete_message');
        return;
    }

    try {
        const [chat] = await query('SELECT * FROM chats WHERE id_chat = ?', [id_chat]);
        
        if (!chat) {
            console.error('Message not found for delete');
            return;
        }

        await query('DELETE FROM chats WHERE id_chat = ?', [id_chat]);

        const payload = JSON.stringify({
            type: 'message_deleted',
            chat_id: id_chat,
        });

        sendTo(String(chat.sender_id), payload);
        sendTo(String(chat.receiver_id), payload);
        
        console.log(`🗑️ Message deleted: ${id_chat}`);
    } catch (err) {
        console.error('Error in handleDeleteMessage:', err.message);
        throw err;
    }
}

function handleTyping(data) {
    const { senderId, receiverId, isTyping } = data;
    
    if (!senderId || !receiverId) {
        console.error('Missing required fields for typing indicator');
        return;
    }

    const payload = JSON.stringify({
        type: 'typing',
        senderId,
        receiverId,
        isTyping,
    });

    sendTo(String(receiverId), payload);
}

function sendTo(userId, payload) {
    const ws = clients.get(userId);
    if (ws && ws.readyState === 1) {
        try {
            ws.send(payload);
        } catch (err) {
            console.error(`Failed to send to user ${userId}:`, err.message);
        }
    } else {
        console.log(`User ${userId} is not connected or not ready`);
    }
}

function broadcastToAll(payload, excludeUserId = null) {
    clients.forEach((ws, userId) => {
        if (userId !== excludeUserId && ws.readyState === 1) {
            try {
                ws.send(payload);
            } catch (err) {
                console.error(`Failed to broadcast to user ${userId}:`, err.message);
            }
        }
    });
}

// Graceful shutdown
process.on('SIGINT', () => {
    console.log('\n🛑 Shutting down WebSocket server...');
    
    // Close all client connections
    clients.forEach((ws) => {
        ws.close(1000, 'Server shutting down');
    });
    
    // Close WebSocket server
    wss.close(() => {
        console.log('✅ WebSocket server closed');
        
        // Close database connections
        db.end((err) => {
            if (err) {
                console.error('Error closing database:', err.message);
            } else {
                console.log('✅ Database connections closed');
            }
            process.exit(0);
        });
    });
});

console.log('Press Ctrl+C to stop the server');
