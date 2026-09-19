<?php

namespace App\Http\Controllers;

use App\Models\ChatMobile;
use App\Models\UserMobile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
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

    public function index()
    {
        $adminId = Auth::id();
        $users = $this->getConversationList($adminId);

        return view('backend.layout.page_admin.chat', compact('users', 'adminId'));
    }

    public function show($userId)
    {
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

        if (request()->ajax()) {
            return response()->json([
                'chats' => $chats,
                'user' => $user,
                'admin_id' => $adminId,
            ]);
        }

        $conversations = $this->getConversationList($adminId);

        return view('backend.layout.page_admin.chat', [
            'users' => $conversations,
            'activeUser' => $user,
            'chats' => $chats,
            'adminId' => $adminId,
        ]);
    }

    public function send(Request $request)
    {
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

    public function fetchMessages($userId)
    {
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
            'chats' => $chats,
            'admin_id' => $adminId,
        ]);
    }
}
