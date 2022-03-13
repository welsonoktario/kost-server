<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Znck\Eloquent\Traits\BelongsToThrough;

class Room extends Model
{
    use BelongsToThrough;

    protected $guarded = ['id'];

    public function kost()
    {
        return $this->belongsToThrough(Kost::class, RoomType::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
