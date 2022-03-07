<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $guarded = ['id', 'kost_id'];

    public function kost()
    {
        return $this->belongsTo(Kost::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
