<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $guarded = ['id', 'chat_room_id'];
    public $casts = ['is_owner' => 'boolean'];

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function tenant()
    {
        return $this->belongsToThrough(Tenant::class, ChatRoom::class);
    }
}
