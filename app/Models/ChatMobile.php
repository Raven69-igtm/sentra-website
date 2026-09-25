<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMobile extends Model
{
    use HasFactory;

    protected $table = 'chats';
    protected $primaryKey = 'id_chat';

    protected $fillable = ['sender_id', 'receiver_id', 'message', 'is_read', 'is_notified', 'replied_to_id'];

    public function repliedTo()
    {
        return $this->belongsTo(ChatMobile::class, 'replied_to_id', 'id_chat');
    }

    public function sender()
    {
        return $this->belongsTo(UserMobile::class, 'sender_id', 'id_akun');
    }

    public function receiver()
    {
        return $this->belongsTo(UserMobile::class, 'receiver_id', 'id_akun');
    }
}
