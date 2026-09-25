# 📱 Panduan WebSocket untuk Flutter Web (Chrome)

Dokumentasi untuk mengintegrasikan WebSocket dengan Flutter yang dijalankan di Chrome.

---

## 🔌 Koneksi WebSocket

### URL Koneksi
```
ws://localhost:3021
```

**Catatan:** Karena Flutter web berjalan di Chrome di komputer yang sama dengan server, gunakan `localhost`.

---

## 📦 Package yang Diperlukan

Tambahkan di `pubspec.yaml`:

```yaml
dependencies:
  web_socket_channel: ^2.4.0
```

Install:
```bash
flutter pub get
```

---

## 💻 Implementasi Flutter

### 1. Import Package

```dart
import 'package:web_socket_channel/web_socket_channel.dart';
import 'dart:convert';
```

### 2. Setup WebSocket Connection

```dart
class ChatService {
  WebSocketChannel? _channel;
  String userId;
  
  ChatService({required this.userId});
  
  void connect() {
    try {
      _channel = WebSocketChannel.connect(
        Uri.parse('ws://localhost:3021'),
      );
      
      print('✅ Connected to WebSocket');
      
      // Register user setelah koneksi
      register();
      
      // Listen untuk pesan masuk
      _channel!.stream.listen(
        (message) {
          final data = jsonDecode(message);
          _handleMessage(data);
        },
        onError: (error) {
          print('❌ WebSocket Error: $error');
          _reconnect();
        },
        onDone: () {
          print('⚠️ WebSocket Closed');
          _reconnect();
        },
      );
    } catch (e) {
      print('❌ Connection Error: $e');
      _reconnect();
    }
  }
  
  void register() {
    if (_channel != null) {
      _channel!.sink.add(jsonEncode({
        'type': 'register',
        'user_id': userId,
        'user_type': 'flutter_web'
      }));
      print('📝 User registered: $userId');
    }
  }
  
  void _handleMessage(Map<String, dynamic> data) {
    print('📨 Received: ${data['type']}');
    
    switch (data['type']) {
      case 'connected':
        print('✅ WebSocket connection established');
        break;
        
      case 'registered':
        print('✅ User registered successfully');
        print('Online users: ${data['online_users']}');
        break;
        
      case 'new_message':
        print('💬 New message from ${data['sender_id']}: ${data['message']}');
        // Handle new message (update UI, show notification, etc.)
        break;
        
      case 'message_edited':
        print('✏️ Message edited: ${data['chat_id']}');
        // Handle edited message
        break;
        
      case 'message_deleted':
        print('🗑️ Message deleted: ${data['chat_id']}');
        // Handle deleted message
        break;
        
      case 'typing':
        print('⌨️ User ${data['senderId']} is typing...');
        // Show typing indicator
        break;
        
      case 'user_online':
        print('🟢 User ${data['user_id']} is online');
        // Update user status
        break;
        
      case 'user_offline':
        print('⚫ User ${data['user_id']} is offline');
        // Update user status
        break;
        
      case 'error':
        print('❌ Server Error: ${data['message']}');
        break;
    }
  }
  
  void _reconnect() {
    print('🔄 Reconnecting in 3 seconds...');
    Future.delayed(Duration(seconds: 3), () {
      connect();
    });
  }
  
  void sendMessage(String receiverId, String message, {String? replyToId}) {
    if (_channel != null) {
      _channel!.sink.add(jsonEncode({
        'type': 'send_message',
        'sender_id': userId,
        'receiver_id': receiverId,
        'message': message,
        'replied_to_id': replyToId,
      }));
      print('📤 Message sent to $receiverId');
    }
  }
  
  void editMessage(String chatId, String newMessage) {
    if (_channel != null) {
      _channel!.sink.add(jsonEncode({
        'type': 'edit_message',
        'id_chat': chatId,
        'new_message': newMessage,
      }));
      print('✏️ Message edited: $chatId');
    }
  }
  
  void deleteMessage(String chatId) {
    if (_channel != null) {
      _channel!.sink.add(jsonEncode({
        'type': 'delete_message',
        'id_chat': chatId,
      }));
      print('🗑️ Message deleted: $chatId');
    }
  }
  
  void sendTypingIndicator(String receiverId, bool isTyping) {
    if (_channel != null) {
      _channel!.sink.add(jsonEncode({
        'type': 'typing',
        'senderId': userId,
        'receiverId': receiverId,
        'isTyping': isTyping,
      }));
    }
  }
  
  void disconnect() {
    _channel?.sink.close();
    print('👋 Disconnected from WebSocket');
  }
}
```

### 3. Menggunakan di Widget

```dart
class ChatScreen extends StatefulWidget {
  @override
  _ChatScreenState createState() => _ChatScreenState();
}

class _ChatScreenState extends State<ChatScreen> {
  late ChatService _chatService;
  final TextEditingController _messageController = TextEditingController();
  
  @override
  void initState() {
    super.initState();
    
    // Inisialisasi chat service dengan user ID
    _chatService = ChatService(userId: '1'); // Ganti dengan ID user yang login
    _chatService.connect();
  }
  
  @override
  void dispose() {
    _chatService.disconnect();
    _messageController.dispose();
    super.dispose();
  }
  
  void _sendMessage() {
    final message = _messageController.text.trim();
    if (message.isNotEmpty) {
      _chatService.sendMessage(
        '2', // Ganti dengan receiver ID
        message,
      );
      _messageController.clear();
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Chat')),
      body: Column(
        children: [
          Expanded(
            child: ListView.builder(
              itemCount: messages.length,
              itemBuilder: (context, index) {
                // Build message item
                return ListTile(
                  title: Text(messages[index]),
                );
              },
            ),
          ),
          Padding(
            padding: EdgeInsets.all(8.0),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _messageController,
                    decoration: InputDecoration(
                      hintText: 'Type a message...',
                    ),
                    onChanged: (text) {
                      // Send typing indicator
                      _chatService.sendTypingIndicator('2', text.isNotEmpty);
                    },
                  ),
                ),
                IconButton(
                  icon: Icon(Icons.send),
                  onPressed: _sendMessage,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
```

---

## 🚀 Menjalankan

### 1. Jalankan WebSocket Server

Di terminal/command prompt:
```bash
npm run ws
```

Output yang diharapkan:
```
✅ Database connected successfully
🚀 WebSocket server running on 127.0.0.1:3021
💻 Connect using: ws://localhost:3021
   For Flutter web in Chrome, use the same URL
Press Ctrl+C to stop the server
```

### 2. Jalankan Flutter Web

Di terminal project Flutter:
```bash
flutter run -d chrome
```

---

## 🔍 Testing

### Test Koneksi dari Browser Console

Buka Developer Tools (F12) di Chrome dan jalankan:

```javascript
const ws = new WebSocket('ws://localhost:3021');

ws.onopen = () => {
    console.log('✅ Connected!');
    ws.send(JSON.stringify({
        type: 'register',
        user_id: '1',
        user_type: 'web_test'
    }));
};

ws.onmessage = (e) => {
    console.log('📨 Received:', JSON.parse(e.data));
};

// Test kirim pesan
ws.send(JSON.stringify({
    type: 'send_message',
    sender_id: '1',
    receiver_id: '2',
    message: 'Hello from browser!'
}));
```

---

## 🎯 Contoh Lengkap dengan State Management

### Menggunakan Provider

```dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class ChatProvider extends ChangeNotifier {
  ChatService? _chatService;
  List<Message> _messages = [];
  
  List<Message> get messages => _messages;
  
  void connect(String userId) {
    _chatService = ChatService(userId: userId);
    _chatService!.connect();
    // Setup listener untuk update messages
  }
  
  void sendMessage(String receiverId, String message) {
    _chatService?.sendMessage(receiverId, message);
  }
  
  void addMessage(Message message) {
    _messages.add(message);
    notifyListeners();
  }
}

// Di main.dart
void main() {
  runApp(
    ChangeNotifierProvider(
      create: (_) => ChatProvider(),
      child: MyApp(),
    ),
  );
}

// Di widget
class ChatScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final chatProvider = Provider.of<ChatProvider>(context);
    
    return Scaffold(
      body: ListView.builder(
        itemCount: chatProvider.messages.length,
        itemBuilder: (context, index) {
          return MessageTile(message: chatProvider.messages[index]);
        },
      ),
    );
  }
}
```

---

## ⚠️ Troubleshooting

### 1. Connection Failed

**Error:** `WebSocketChannelException: WebSocketChannelException: Failed to connect to 'ws://localhost:3021'`

**Solusi:**
- Pastikan WebSocket server sudah berjalan (`npm run ws`)
- Cek output server, pastikan ada pesan "WebSocket server running"
- Pastikan tidak ada aplikasi lain yang menggunakan port 3021

### 2. Chrome CORS Issue

Jika ada error CORS, tambahkan flag saat menjalankan Chrome:

```bash
flutter run -d chrome --web-browser-flag "--disable-web-security"
```

**Catatan:** Hanya untuk development!

### 3. Hot Reload Disconnect

Setelah hot reload, koneksi WebSocket akan terputus. Implementasikan auto-reconnect:

```dart
@override
void didUpdateWidget(ChatScreen oldWidget) {
  super.didUpdateWidget(oldWidget);
  // Reconnect if needed
  if (!_chatService.isConnected) {
    _chatService.connect();
  }
}
```

---

## 📝 Best Practices

### 1. Handle Connection State

```dart
enum ConnectionState { disconnected, connecting, connected }

class ChatService {
  ConnectionState _state = ConnectionState.disconnected;
  
  ConnectionState get state => _state;
  
  void connect() {
    _state = ConnectionState.connecting;
    // ... connection code
  }
}
```

### 2. Implement Retry Logic

```dart
int _retryCount = 0;
final int _maxRetries = 5;

void _reconnect() {
  if (_retryCount < _maxRetries) {
    _retryCount++;
    final delay = Duration(seconds: _retryCount * 2);
    print('🔄 Reconnecting in ${delay.inSeconds} seconds... (Attempt $_retryCount/$_maxRetries)');
    Future.delayed(delay, connect);
  } else {
    print('❌ Max retry attempts reached');
  }
}
```

### 3. Clean Up Resources

```dart
@override
void dispose() {
  _channel?.sink.close();
  super.dispose();
}
```

---

## 🎉 Testing Komunikasi

### Skenario Test:

1. **Web → Flutter:** 
   - Buka console browser, kirim pesan
   - Pesan muncul di Flutter app

2. **Flutter → Web:**
   - Kirim pesan dari Flutter app
   - Pesan muncul di browser console

3. **Flutter → Flutter:**
   - Buka 2 window Chrome dengan Flutter app
   - Kirim pesan dari satu window
   - Pesan muncul di window lainnya

---

## 📌 Checklist

- [ ] WebSocket server berjalan
- [ ] Flutter project sudah install `web_socket_channel`
- [ ] Koneksi menggunakan `ws://localhost:3021`
- [ ] Register user setelah koneksi
- [ ] Handle semua tipe message
- [ ] Implement reconnection logic
- [ ] Clean up resources di dispose

---

**Selamat menggunakan WebSocket di Flutter Web! 🎉**

Jika ada masalah, cek console browser (F12) dan console server untuk error messages.
