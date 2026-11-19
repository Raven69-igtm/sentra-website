<?php

namespace App\Http\Controllers;

use App\Models\ChatMobile;
use App\Models\UserMobile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Chat konsultasi hanya untuk Admin PPA. Admin Dinsos dialihkan ke
     * dashboard-nya (pola sama seperti LaporanCepatController), termasuk
     * untuk request AJAX agar polling tidak diam-diam tetap berjalan.
     */
    private function denyAdminDinsos(Request $request)
    {
        if (! Auth::user()?->isAdminDinsos()) {
            return null;
        }

        if ($request->expectsJson() || $request->ajax()) {
            abort(403, 'Fitur chat konsultasi tidak tersedia untuk Admin Dinsos.');
        }

        return redirect()->route('dinsos.dashboard')
            ->with('error', 'Fitur chat konsultasi tidak tersedia untuk Admin Dinsos.');
    }

    /**
     * Bentuk data chat yang dipakai klien (ISO date agar aman di semua
     * browser) — dipakai untuk preload maupun fetch JSON.
     */
    private function serializeChat(ChatMobile $chat): array
    {
        return [
            'id_chat' => (int) $chat->id_chat,
            'sender_id' => (int) $chat->sender_id,
            'receiver_id' => (int) $chat->receiver_id,
            'message' => $chat->message,
            'sent_at' => Carbon::parse($chat->sent_at)->toIso8601String(),
        ];
    }

    /**
     * Semua pesan untuk seluruh percakapan, dikelompokkan per lawan bicara.
     * Dikirim bersama halaman agar klik percakapan bisa dirender instan
     * tanpa menunggu request ke server.
     */
    private function getChatPrefetch($adminId, $userIds): array
    {
        $userIds = collect($userIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($userIds->isEmpty()) {
            return [];
        }

        $ids = $userIds->all();

        $chats = ChatMobile::where(function ($q) use ($adminId, $ids) {
            $q->where('sender_id', $adminId)->whereIn('receiver_id', $ids);
        })->orWhere(function ($q) use ($adminId, $ids) {
            $q->where('receiver_id', $adminId)->whereIn('sender_id', $ids);
        })->orderBy('sent_at', 'asc')->get();

        $prefetch = [];

        foreach ($chats as $chat) {
            $otherId = (string) ($chat->sender_id == $adminId ? $chat->receiver_id : $chat->sender_id);
            $prefetch[$otherId][] = $this->serializeChat($chat);
        }

        return $prefetch;
    }

    private function getConversationList($adminId)
    {
        $uId = intval($adminId);
        $subQuery = DB::raw("(
            SELECT 
                IF(sender_id = $uId, receiver_id, sender_id) AS other_user_id,
                id_chat
            FROM chats
            WHERE (sender_id = $uId OR receiver_id = $uId)
            AND sender_id != receiver_id
        ) AS user_chats");

        $conversations = DB::table($subQuery)
            ->select('other_user_id', DB::raw('MAX(id_chat) AS latest_chat_id'))
            ->groupBy('other_user_id')
            ->orderBy('latest_chat_id', 'desc')
            ->get();

        return $conversations->map(function ($item) use ($adminId) {
            // Skip jika other_user_id sama dengan adminId (self-chat)
            if ($item->other_user_id == $adminId) return null;
            
            $latestChat = ChatMobile::find($item->latest_chat_id);
            $user = UserMobile::where('id_akun', $item->other_user_id)->first();
            if (!$user || !$latestChat) return null;

            $unread = ChatMobile::where('sender_id', $item->other_user_id)
                ->where('receiver_id', $adminId)
                ->where('is_read', 0)
                ->count();

            return (object) [
                'id_akun' => $user->id_akun,
                'nama' => $user->nama,
                'last_message' => $latestChat->message,
                'time' => $latestChat->sent_at,
                'unread' => $unread,
            ];
        })->filter()->values();
    }

    public function index(Request $request)
    {
        if ($deny = $this->denyAdminDinsos($request)) {
            return $deny;
        }

        $adminId = Auth::id();
        $users = $this->getConversationList($adminId);
        $chatPrefetch = $this->getChatPrefetch($adminId, $users->pluck('id_akun'));

        return view('backend.layout.page_admin.chat', compact('users', 'adminId', 'chatPrefetch'));
    }

    public function show(Request $request, $userId)
    {
        if ($deny = $this->denyAdminDinsos($request)) {
            return $deny;
        }

        $adminId = Auth::id();

        ChatMobile::where('sender_id', $userId)
            ->where('receiver_id', $adminId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        $chats = ChatMobile::where(function ($q) use ($adminId, $userId) {
            $q->where('sender_id', $adminId)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($adminId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $adminId);
        })->orderBy('sent_at', 'asc')->get();

        $user = UserMobile::where('id_akun', $userId)->first();

        if (request()->wantsJson()) {
            return response()->json([
                'chats' => $chats->map(fn ($chat) => $this->serializeChat($chat))->values(),
                'user' => $user,
                'admin_id' => (int) $adminId,
            ]);
        }

        $conversations = $this->getConversationList($adminId);

        return view('backend.layout.page_admin.chat', [
            'users' => $conversations,
            'activeUser' => $user,
            'chats' => $chats,
            'adminId' => $adminId,
            'chatPrefetch' => $this->getChatPrefetch($adminId, $conversations->pluck('id_akun')),
        ]);
    }

    public function send(Request $request)
    {
        if ($deny = $this->denyAdminDinsos($request)) {
            return $deny;
        }

        $enabled = (string) \App\Models\SystemSetting::get('enable_chat_consultation', '1');
        if ($enabled === '0') {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan konsultasi chat sedang dinonaktifkan sementara oleh Superadmin.'
                ], 403);
            }
            return back()->with('error', 'Layanan konsultasi chat sedang dinonaktifkan sementara oleh Superadmin.');
        }

        $request->validate([
            'receiver_id' => 'required|exists:akun,id_akun',
            'message' => 'required|string|max:5000',
        ]);

        $chat = ChatMobile::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'chat' => $chat]);
        }

        return redirect()->route('chat.show', $request->receiver_id);
    }

    public function fetchMessages(Request $request, $userId)
    {
        if ($deny = $this->denyAdminDinsos($request)) {
            return $deny;
        }

        $adminId = Auth::id();

        ChatMobile::where('sender_id', $userId)
            ->where('receiver_id', $adminId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        $chats = ChatMobile::where(function ($q) use ($adminId, $userId) {
            $q->where('sender_id', $adminId)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($adminId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $adminId);
        })->orderBy('sent_at', 'asc')->get();

        return response()->json([
            'chats' => $chats->map(fn ($chat) => $this->serializeChat($chat))->values(),
            'admin_id' => (int) $adminId,
        ]);
    }
}
