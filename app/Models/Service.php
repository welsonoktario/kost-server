<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = ['id', 'kost_id'];

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class);
    }
}
