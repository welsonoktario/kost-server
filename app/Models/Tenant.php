<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Znck\Eloquent\Traits\BelongsToThrough;

class Tenant extends Model
{
    use SoftDeletes, BelongsToThrough;

    protected $guarded = ['id', 'kost_id'];

    protected $fillable = [
        'entry_date',
        'leave_date',
        'due_date',
        'status',
        'ktp',
        'deleted_at'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->hasOne(Room::class);
    }

    public function services()
    {
        return $this->hasMany(TenantService::class)->with('service');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function additionals()
    {
        return $this->hasMany(Additional::class);
    }

    public function complains()
    {
        return $this->hasMany(Complain::class);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notificationable');
    }

    public function chatRoom()
    {
        return $this->hasOne(ChatRoom::class);
    }

    public function dendas()
    {
        return $this->hasMany(Denda::class);
    }
}
