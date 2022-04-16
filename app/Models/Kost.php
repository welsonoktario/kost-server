<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kost extends Model
{
    protected $guarded = ['id', 'user_username'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rooms()
    {
        return $this->hasManyThrough(Room::class, RoomType::class);
    }

    public function tenants()
    {
        return $this->hasManyThrough(Tenant::class, Room::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }

    public function images()
    {
        return $this->hasMany(KostImage::class);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notificationable');
    }
}
