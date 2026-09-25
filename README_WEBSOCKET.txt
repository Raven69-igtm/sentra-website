=====================================
    QUICK START - WebSocket Server
=====================================

📋 LANGKAH CEPAT:

1. Install Dependencies
   npm install

2. Pastikan MySQL Running
   net start MySQL80

3. Jalankan WebSocket Server
   npm run ws
   
   ATAU double-click file:
   start-websocket.bat

4. KONEKSI (untuk Web & Flutter Web di Chrome)
   ws://localhost:3021

=====================================
    FLUTTER WEB DI CHROME
=====================================

Karena Flutter dijalankan di Chrome di komputer
yang sama, gunakan LOCALHOST:

URL WebSocket:
ws://localhost:3021

Contoh di Flutter:
WebSocketChannel.connect(
  Uri.parse('ws://localhost:3021')
);

Dokumentasi lengkap Flutter:
FLUTTER_WEBSOCKET.md

=====================================
    TEST KONEKSI
=====================================

Dari Browser Console (F12):

const ws = new WebSocket('ws://localhost:3021');
ws.onopen = () => {
    console.log('✅ Connected!');
    ws.send(JSON.stringify({
        type: 'register',
        user_id: '1',
        user_type: 'web'
    }));
};
ws.onmessage = (e) => console.log(JSON.parse(e.data));

=====================================
    TROUBLESHOOTING
=====================================

❌ Port already in use
   → netstat -ano | findstr :3021
   → taskkill /PID <PID> /F

❌ Database connection failed
   → Cek MySQL service running
   → Cek config di .env

❌ Flutter tidak bisa connect
   → Pastikan WebSocket server running
   → Gunakan ws://localhost:3021
   → Cek console browser (F12) untuk error

=====================================

Dokumentasi lengkap: WEBSOCKET_GUIDE.md
Flutter Web Guide: FLUTTER_WEBSOCKET.md

Konfigurasi: .env

Scripts:
- npm run ws          → Jalankan WebSocket
- npm run start:all   → Jalankan semua

=====================================
