<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = ['id'];

    public function kost()
    {
        return $this->belongsTo(Kost::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
