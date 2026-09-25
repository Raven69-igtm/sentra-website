<?php

namespace App\Http\Controllers;

use App\Models\ChatMobile;
use App\Models\UserMobile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatControllerMobile extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:akun,id_akun',
            'receiver_id' => 'required|exists:akun,id_akun',
            'message' => 'required|string',
        ]);

        $chat = ChatMobile::create([
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        try {
            $notificationController = new NotificationControllerMobile();
            $notificationRequest = new Request([
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
            ]);
            $notificationController->newMessage($notificationRequest);
        } catch (\Exception $e) {
            \Log::error('FCM notification failed: ' . $e->getMessage());
        }

        return response()->json($chat, 201);
    }

    public function getChats($userId, Request $request)
    {
        try {
            $query = ChatMobile::where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            });

            if ($request->has('other_user_id')) {
                $otherUserId = $request->other_user_id;
                $query = ChatMobile::where(function ($q) use ($userId, $otherUserId) {
                    $q->where(function ($q2) use ($userId, $otherUserId) {
                        $q2->where('sender_id', $userId)->where('receiver_id', $otherUserId);
                    })->orWhere(function ($q2) use ($userId, $otherUserId) {
                        $q2->where('sender_id', $otherUserId)->where('receiver_id', $userId);
                    });
                });
            }

            $chats = $query->orderBy('sent_at', 'asc')
                ->get()
                ->map(function ($chat) {
                    return [
                        'id_chat' => $chat->id_chat,
                        'sender_id' => $chat->sender_id,
                        'receiver_id' => $chat->receiver_id,
                        'message' => $chat->message,
                        'replied_to_id' => $chat->replied_to_id,
                        'sent_at' => $chat->sent_at,
                        'is_read' => $chat->is_read ? 1 : 0,
                        'is_notified' => $chat->is_notified ? 1 : 0,
                    ];
                });

            return response()->json($chats);
        } catch (\Exception $e) {
            \Log::error('Error in getChats: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch chats'], 500);
        }
    }

    public function markAsRead($chatId)
    {
        $chat = ChatMobile::where('id_chat', $chatId)->firstOrFail();
        $chat->update(['is_read' => 1]);

        return response()->json($chat);
    }

    public function getConversations($userId)
    {
        try {
            $uId = intval($userId);
            $subQuery = DB::raw("(
                SELECT 
                    IF(sender_id = $uId, receiver_id, sender_id) AS other_user_id,
                    id_chat
                FROM chats
                WHERE sender_id = $uId OR receiver_id = $uId
            ) AS user_chats");

            $latestChats = DB::table($subQuery)
                ->select('other_user_id', DB::raw('MAX(id_chat) AS latest_chat_id'))
                ->groupBy('other_user_id')
                ->orderBy('latest_chat_id', 'desc')
                ->get();

            $conversations = $latestChats->map(function ($item) {
                $latestChat = ChatMobile::find($item->latest_chat_id);
                if (!$latestChat) return null;

                $otherUser = UserMobile::select('nama')->where('id_akun', $item->other_user_id)->first();

                return [
                    'user_id' => $item->other_user_id,
                    'name' => $otherUser ? $otherUser->nama : 'Unknown',
                    'last_message' => $latestChat->message,
                    'time' => $latestChat->sent_at,
                    'is_online' => false,
                ];
            })->filter()->values();

            return response()->json($conversations);
        } catch (\Exception $e) {
            \Log::error('Error in getConversations: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch conversations'], 500);
        }
    }

    public function update(Request $request, $chatId)
    {
        $request->validate(['message' => 'required|string']);

        $chat = ChatMobile::where('id_chat', $chatId)->firstOrFail();
        $chat->update(['message' => $request->message]);

        return response()->json($chat);
    }

    public function destroy($chatId)
    {
        $chat = ChatMobile::where('id_chat', $chatId)->firstOrFail();
        $chat->delete();

        return response()->json(['message' => 'Chat deleted']);
    }

    public function destroyConversation($senderId, $receiverId)
    {
        ChatMobile::where(function ($q) use ($senderId, $receiverId) {
            $q->where('sender_id', $senderId)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($senderId, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $senderId);
        })->delete();

        return response()->json(['message' => 'Conversation deleted']);
    }
}
