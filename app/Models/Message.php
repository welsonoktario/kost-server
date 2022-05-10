<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = ['id', 'chat_room_id'];

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }
}
