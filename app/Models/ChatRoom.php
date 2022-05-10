<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $guarded = ['id'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function kost()
    {
        return $this->belongsTo(Kost::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
