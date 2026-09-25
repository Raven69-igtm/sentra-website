# 🔧 Perbaikan Flutter Mobile - Kirim Chat ke Admin yang Benar

## ❌ Masalah Sekarang:

Chat dari mobile (aripJMK48) **tidak masuk** ke Admin di web karena `receiver_id` nya salah.

Saat ini:
- `sender_id`: 1 (aripJMK48)  
- `receiver_id`: 1 (ke diri sendiri) ❌

Yang benar:
- `sender_id`: 1 (aripJMK48)
- `receiver_id`: 15 (Admin Sentra) ✅

---

## ✅ Cara Perbaiki:

### **1. Buka File Chat Controller**

Lokasi: `C:\Projects\keloompok3C-Sentra-\lib\features\chat\controllers\chat_controller.dart`

### **2. Cari Function `sendMessage`**

Function ini kemungkinan seperti ini:

```dart
void sendMessage(int senderId, int receiverId, String message, {int? repliedToId}) {
  if (_channel != null && _channel!.ready) {
    _channel!.sink.add(jsonEncode({
      'type': 'send_message',
      'sender_id': senderId.toString(),
      'receiver_id': receiverId.toString(),  // ← Yang ini harus diubah!
      'message': message,
      'replied_to_id': repliedToId,
    }));
  }
}
```

### **3. PERBAIKAN - Hardcode receiver_id ke Admin**

**OPSI A: Hardcode ke Admin ID 15 (Temporary Fix)**

```dart
void sendMessage(int senderId, int receiverId, String message, {int? repliedToId}) {
  if (_channel != null && _channel!.ready) {
    _channel!.sink.add(jsonEncode({
      'type': 'send_message',
      'sender_id': senderId.toString(),
      'receiver_id': '15',  // ← HARDCODE KE ADMIN ID 15
      'message': message,
      'replied_to_id': repliedToId,
    }));
  }
}
```

**OPSI B: Buat Constant untuk Admin ID (Better)**

Tambahkan di file constants:

```dart
// File: lib/core/constants/app_constants.dart
class AppConstants {
  static const int ADMIN_ID = 15;  // ID Admin Sentra
}
```

Lalu gunakan:

```dart
import 'package:sentra/core/constants/app_constants.dart';

void sendMessage(int senderId, int receiverId, String message, {int? repliedToId}) {
  if (_channel != null && _channel!.ready) {
    _channel!.sink.add(jsonEncode({
      'type': 'send_message',
      'sender_id': senderId.toString(),
      'receiver_id': AppConstants.ADMIN_ID.toString(),  // ← Pakai constant
      'message': message,
      'replied_to_id': repliedToId,
    }));
  }
}
```

**OPSI C: Fetch Admin ID dari API (Best Practice)**

```dart
class ChatController extends ChangeNotifier {
  int? _adminId;
  
  // Fetch admin ID saat init
  Future<void> fetchAdminId() async {
    try {
      final response = await dio.get('${ApiClient.baseUrl}/api/admin-id');
      _adminId = response.data['admin_id'];
    } catch (e) {
      _adminId = 15; // Fallback ke default
    }
  }
  
  void sendMessage(int senderId, int receiverId, String message, {int? repliedToId}) {
    if (_channel != null && _channel!.ready && _adminId != null) {
      _channel!.sink.add(jsonEncode({
        'type': 'send_message',
        'sender_id': senderId.toString(),
        'receiver_id': _adminId.toString(),  // ← Dari API
        'message': message,
        'replied_to_id': repliedToId,
      }));
    }
  }
}
```

---

## 🔧 Perbaikan di Chat Screen (Jika Perlu)

### **Cari di file:** `chat_screen.dart`

Function `_sendMessage()` kemungkinan seperti ini:

```dart
void _sendMessage() {
  final message = _controller.text.trim();
  if (message.isNotEmpty) {
    final chatController = Provider.of<ChatController>(context, listen: false);
    chatController.sendMessage(
      _idUser!,
      widget.receiverId,  // ← Ini mungkin salah!
      message,
      repliedToId: chatController.repliedToChat?.idChat,
    );
    _controller.clear();
  }
}
```

**UBAH JADI:**

```dart
void _sendMessage() {
  final message = _controller.text.trim();
  if (message.isNotEmpty) {
    final chatController = Provider.of<ChatController>(context, listen: false);
    chatController.sendMessage(
      _idUser!,
      15,  // ← HARDCODE ke Admin ID 15
      message,
      repliedToId: chatController.repliedToChat?.idChat,
    );
    _controller.clear();
  }
}
```

---

## 📝 Langkah-langkah:

### **1. Edit File Chat Controller**

```bash
# Buka dengan editor
code C:\Projects\keloompok3C-Sentra-\lib\features\chat\controllers\chat_controller.dart
```

### **2. Cari function `sendMessage`**

Tekan `Ctrl+F` dan cari "sendMessage"

### **3. Ubah receiver_id**

Ganti:
```dart
'receiver_id': receiverId.toString(),
```

Jadi:
```dart
'receiver_id': '15',  // ID Admin Sentra
```

### **4. Save dan Test**

```bash
# Stop flutter (Ctrl+C)
# Run lagi
flutter run -d chrome
```

---

## 🧪 Testing:

### **1. Login di Mobile**
- Email: arip@gmali.com (aripJMK48)
- Password: (password lama)

### **2. Login di Web**
- URL: http://127.0.0.1:8000/login
- Email: admin@sentra.com
- Password: admin123

### **3. Kirim Pesan dari Mobile**
- Buka chat
- Ketik: "Halo Admin, test dari mobile"
- Kirim

### **4. Cek di Web Admin**
- Refresh halaman chat: http://127.0.0.1:8000/chat
- Seharusnya muncul **aripJMK48** di list
- Klik aripJMK48
- Chat "Halo Admin, test dari mobile" akan muncul!

---

## 🎯 Verifikasi Berhasil:

✅ Chat dari mobile muncul di list percakapan web admin  
✅ Bubble putih di kiri (dari user mobile)  
✅ Admin bisa balas, bubble hijau di kanan  
✅ Balasan admin muncul di mobile  

---

## ⚠️ PENTING:

**Pastikan WebSocket server berjalan:**

```bash
cd C:\Projects\Sentra
npm run ws
```

Output:
```
✅ Database connected successfully
🚀 WebSocket server running on 127.0.0.1:3021
💻 Connect using: ws://localhost:3021
```

---

## 🆘 Jika Masih Belum Berhasil:

### **Debug di Browser Console (F12):**

```javascript
// Cek koneksi WebSocket
console.log(ws.readyState);  // Harus = 1 (OPEN)

// Test kirim manual
ws.send(JSON.stringify({
  type: 'send_message',
  sender_id: '1',
  receiver_id: '15',
  message: 'Test manual'
}));
```

### **Cek Database:**

```bash
cd C:\Projects\Sentra
php artisan tinker

# Jalankan:
DB::table('chats')->where('sender_id', 1)->where('receiver_id', 15)->get();
```

Harus muncul chat yang baru dikirim!

---

## 📞 Contact:

Jika masih ada masalah, screenshot:
1. Console error di Flutter (debug console)
2. Console error di browser (F12)
3. Output WebSocket server
4. Hasil query database

---

**Good luck! 🚀**
