<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    protected $guarded = ['id', 'tenant_id'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
