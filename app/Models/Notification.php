<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'date:Y-m-d'
    ];
    public function notificationable()
    {
        return $this->morphTo();
    }
}
