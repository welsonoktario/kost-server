<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['total', 'type'];

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
