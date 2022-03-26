<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $guarded = ['id', 'kost_id'];

    protected $fillable = [
        'entry_date',
        'leave_date',
        'due_date',
        'status',
        'ktp'
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
        return $this->hasMany(Notification::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function dendas()
    {
        return $this->hasMany(Denda::class);
    }
}
