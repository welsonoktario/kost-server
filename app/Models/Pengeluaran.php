<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $guarded = ['id', 'kost_id'];

    public function kost()
    {
        return $this->belongsTo(Kost::class);
    }
}
