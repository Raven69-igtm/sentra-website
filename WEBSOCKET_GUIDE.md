# 📡 Panduan WebSocket Server - Sentra

Dokumentasi lengkap untuk setup dan menggunakan WebSocket server pada aplikasi Sentra untuk komunikasi real-time antara aplikasi web dan mobile.

---

## 📋 Daftar Isi

1. [Instalasi](#instalasi)
2. [Konfigurasi](#konfigurasi)
3. [Menjalankan Server](#menjalankan-server)
4. [Cara Koneksi](#cara-koneksi)
5. [Format Pesan](#format-pesan)
6. [Troubleshooting](#troubleshooting)

---

## 🔧 Instalasi

### 1. Install Dependencies

```bash
npm install
```

Package yang diperlukan:
- `ws` - WebSocket library
- `mysql` - Database connector
- `dotenv` - Environment variables loader

---

## ⚙️ Konfigurasi

### File `.env`

Pastikan konfigurasi WebSocket di file `.env` sudah benar:

```env
# WebSocket Configuration
WS_PORT=3021
WS_HOST=0.0.0.0
WS_CLIENT_HOST=localhost

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sentra
DB_USERNAME=root
DB_PASSWORD=root
```

**Penjelasan:**
- `WS_PORT`: Port yang digunakan WebSocket server (default: 3021)
- `WS_HOST`: Host server (0.0.0.0 untuk menerima koneksi dari semua network interface)
- `WS_CLIENT_HOST`: Host untuk koneksi client (localhost untuk testing lokal)

### Cek IP Address Komputer

Untuk koneksi dari mobile device, Anda perlu tahu IP address komputer:

**Windows:**
```bash
ipconfig
```
Cari "IPv4 Address" di bagian WiFi atau Ethernet adapter.

**Linux/Mac:**
```bash
ifconfig
# atau
ip addr show
```

Contoh IP: `192.168.1.100`

---

## 🚀 Menjalankan Server

### Opsi 1: Menggunakan npm script (Recommended)

```bash
# Jalankan hanya WebSocket server
npm run websocket
# atau
npm run ws

# Jalankan semua service (WebSocket + Laravel + Vite)
npm run start:all
```

### Opsi 2: Menggunakan script file

**Windows:**
```bash
start-websocket.bat
```

**Linux/Mac:**
```bash
chmod +x start-websocket.sh
./start-websocket.sh
```

### Opsi 3: Langsung dengan Node.js

```bash
node websocket-server.cjs
```

### ✅ Server Berhasil Berjalan

Jika berhasil, Anda akan melihat output seperti ini:

```
✅ Database connected successfully
🚀 WebSocket server running on 0.0.0.0:3021
📱 Mobile devices should connect to: ws://<YOUR_LOCAL_IP>:3021
💻 Web clients should connect to: ws://localhost:3021
   (Replace <YOUR_LOCAL_IP> with your computer's IP address)
Press Ctrl+C to stop the server
```

---

## 🔌 Cara Koneksi

### Untuk Web Client (JavaScript)

```javascript
// Koneksi ke WebSocket server
const ws = new WebSocket('ws://localhost:3021');

ws.onopen = function() {
    console.log('Connected to WebSocket server');
    
    // Register user setelah koneksi
    ws.send(JSON.stringify({
        type: 'register',
        user_id: '123',  // ID user dari database
        user_type: 'web' // 'web' atau 'mobile'
    }));
};

ws.onmessage = function(event) {
    const data = JSON.parse(event.data);
    console.log('Received:', data);
    
    // Handle berbagai tipe message
    switch(data.type) {
        case 'connected':
            console.log('WebSocket connection established');
            break;
        case 'registered':
            console.log('User registered:', data.user_id);
            break;
        case 'new_message':
            console.log('New message:', data.message);
            break;
        case 'typing':
            console.log('User typing:', data.senderId);
            break;
    }
};

ws.onerror = function(error) {
    console.error('WebSocket error:', error);
};

ws.onclose = function() {
    console.log('Disconnected, reconnecting...');
    // Implementasi reconnection logic
    setTimeout(() => {
        // Reconnect
    }, 3000);
};
```

### Untuk Mobile Client (Flutter/React Native)

**Flutter:**
```dart
import 'package:web_socket_channel/web_socket_channel.dart';

// Ganti <YOUR_IP> dengan IP komputer Anda
final channel = WebSocketChannel.connect(
  Uri.parse('ws://192.168.1.100:3021'),
);

// Register user
channel.sink.add(jsonEncode({
  'type': 'register',
  'user_id': '123',
  'user_type': 'mobile'
}));

// Listen untuk pesan
channel.stream.listen((message) {
  final data = jsonDecode(message);
  print('Received: $data');
});
```

**React Native:**
```javascript
// Ganti <YOUR_IP> dengan IP komputer Anda
const ws = new WebSocket('ws://192.168.1.100:3021');

ws.onopen = () => {
    console.log('Connected');
    
    // Register user
    ws.send(JSON.stringify({
        type: 'register',
        user_id: '123',
        user_type: 'mobile'
    }));
};

ws.onmessage = (e) => {
    const data = JSON.parse(e.data);
    console.log('Received:', data);
};
```

---

## 📨 Format Pesan

### 1. Register User

Setelah koneksi, user harus register terlebih dahulu:

**Request:**
```json
{
    "type": "register",
    "user_id": "123",
    "user_type": "web"
}
```

**Response:**
```json
{
    "type": "registered",
    "user_id": "123",
    "user_type": "web",
    "online_users": ["123", "456", "789"]
}
```

### 2. Kirim Pesan

**Request:**
```json
{
    "type": "send_message",
    "sender_id": "123",
    "receiver_id": "456",
    "message": "Hello, this is a test message",
    "replied_to_id": null
}
```

**Response (diterima oleh sender dan receiver):**
```json
{
    "type": "new_message",
    "id_chat": 1001,
    "sender_id": "123",
    "receiver_id": "456",
    "message": "Hello, this is a test message",
    "replied_to_id": null,
    "sent_at": "2026-09-24 15:30:00",
    "is_read": 0,
    "is_notified": 0
}
```

### 3. Edit Pesan

**Request:**
```json
{
    "type": "edit_message",
    "id_chat": 1001,
    "new_message": "Updated message text"
}
```

**Response:**
```json
{
    "type": "message_edited",
    "chat_id": 1001,
    "new_message": "Updated message text",
    "sent_at": "2026-09-24 15:30:00"
}
```

### 4. Hapus Pesan

**Request:**
```json
{
    "type": "delete_message",
    "id_chat": 1001
}
```

**Response:**
```json
{
    "type": "message_deleted",
    "chat_id": 1001
}
```

### 5. Typing Indicator

**Request:**
```json
{
    "type": "typing",
    "senderId": "123",
    "receiverId": "456",
    "isTyping": true
}
```

**Response (diterima oleh receiver):**
```json
{
    "type": "typing",
    "senderId": "123",
    "receiverId": "456",
    "isTyping": true
}
```

### 6. Ping/Pong (Heartbeat)

**Request:**
```json
{
    "type": "ping"
}
```

**Response:**
```json
{
    "type": "pong"
}
```

### 7. Notifikasi User Online/Offline

**User Online:**
```json
{
    "type": "user_online",
    "user_id": "123"
}
```

**User Offline:**
```json
{
    "type": "user_offline",
    "user_id": "123"
}
```

---

## 🔍 Troubleshooting

### Server tidak bisa dijalankan

**Error: Port already in use**

```bash
# Check port yang sedang digunakan
netstat -ano | findstr :3021

# Kill process yang menggunakan port tersebut
taskkill /PID <PID_NUMBER> /F
```

**Error: Database connection failed**

- Pastikan MySQL server berjalan
- Cek konfigurasi database di file `.env`
- Pastikan database `sentra` sudah dibuat

```bash
# Cek MySQL service
sc query MySQL80

# Start MySQL service
net start MySQL80
```

### Mobile tidak bisa connect

1. **Pastikan komputer dan mobile dalam jaringan WiFi yang sama**

2. **Cek firewall Windows:**
   - Buka Windows Defender Firewall
   - Klik "Advanced settings"
   - Klik "Inbound Rules" → "New Rule"
   - Pilih "Port" → Next
   - Pilih "TCP" dan masukkan port "3021" → Next
   - Pilih "Allow the connection" → Next
   - Check semua profile → Next
   - Beri nama "WebSocket Server" → Finish

3. **Test koneksi dari mobile:**

   Gunakan aplikasi WebSocket testing di mobile (contoh: "Simple WebSocket Client")
   
   URL: `ws://192.168.1.100:3021` (ganti dengan IP komputer Anda)

4. **Cek IP address:**

   ```bash
   # Windows
   ipconfig
   
   # Pastikan menggunakan IP dari WiFi adapter
   ```

### Koneksi terputus terus-menerus

Server memiliki **heartbeat mechanism** yang akan otomatis terminate koneksi yang tidak aktif selama 30 detik.

**Solusi:** Implement reconnection logic di client:

```javascript
function connectWebSocket() {
    const ws = new WebSocket('ws://localhost:3021');
    
    ws.onclose = function() {
        console.log('Disconnected, reconnecting in 3 seconds...');
        setTimeout(connectWebSocket, 3000);
    };
    
    return ws;
}

let ws = connectWebSocket();
```

### Pesan tidak sampai

1. **Cek user sudah register:**
   
   Pastikan user sudah mengirim message `register` setelah koneksi.

2. **Cek ID user:**
   
   Pastikan `sender_id` dan `receiver_id` sudah benar dan ada di database.

3. **Cek console log server:**
   
   Server akan menampilkan log untuk setiap aktivitas:
   ```
   ✅ User 123 registered (web)
   💬 Message sent: 123 → 456
   ```

4. **Cek readyState WebSocket:**
   
   ```javascript
   console.log(ws.readyState); // 1 = OPEN, 0 = CONNECTING, 2 = CLOSING, 3 = CLOSED
   ```

---

## 📊 Monitoring

### Server Logs

Server menampilkan log real-time dengan emoji untuk memudahkan monitoring:

- `🚀` - Server started
- `✅` - Success operation
- `📲` - New connection
- `💬` - Message sent
- `✏️` - Message edited
- `🗑️` - Message deleted
- `👋` - User disconnected
- `❌` - Error

### Console Commands

Saat server berjalan, Anda bisa melihat:
- Jumlah user yang terkoneksi
- Aktivitas real-time (send, edit, delete message)
- Error dan warning

---

## 🛠️ Development Tips

### Testing dengan Multiple Clients

1. Buka browser di tab berbeda untuk simulasi multiple users
2. Gunakan browser developer tools console untuk test WebSocket
3. Gunakan extension seperti "Simple WebSocket Client" untuk testing

### Debug Mode

Untuk melihat detail query database, tambahkan logging di function `query()`:

```javascript
function query(sql, params = []) {
    console.log('SQL:', sql, 'Params:', params);
    // ... rest of code
}
```

### Production Deployment

Untuk production, pertimbangkan:
1. Gunakan WSS (WebSocket Secure) dengan SSL/TLS
2. Setup reverse proxy (Nginx/Apache)
3. Gunakan PM2 atau systemd untuk process management
4. Monitor dengan tools seperti PM2 monitoring dashboard

---

## 📞 Support

Jika ada masalah atau pertanyaan:

1. Cek log error di console server
2. Cek network tab di browser developer tools
3. Pastikan semua dependency sudah terinstall
4. Pastikan konfigurasi di `.env` sudah benar

---

## 📝 Changelog

### Version 1.0.0 (2026-09-24)
- ✅ WebSocket server dengan koneksi database
- ✅ Support send, edit, delete message
- ✅ Typing indicator
- ✅ User online/offline notification
- ✅ Heartbeat mechanism
- ✅ Graceful shutdown
- ✅ Error handling dan logging

---

**Selamat menggunakan WebSocket Server Sentra! 🎉**
