<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    protected $guarded = ['id', 'transaksi_id'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
