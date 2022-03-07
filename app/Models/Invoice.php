<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = ['id', 'tenant_id'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function dendas()
    {
        return $this->hasMany(Denda::class);
    }
}
