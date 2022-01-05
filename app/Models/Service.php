<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Service::class);
    }
}
