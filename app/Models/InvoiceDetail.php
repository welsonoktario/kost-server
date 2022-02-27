<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'cost'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
