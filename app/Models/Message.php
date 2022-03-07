<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = ['id', 'chat_room_id', 'tenant_id'];

    public function chat_room()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
