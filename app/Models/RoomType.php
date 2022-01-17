<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = ['name', 'cost', 'room_count', 'created_at', 'updated_at'];

    public function kost()
    {
        return $this->belongsTo(Kost::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
