<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'tax_number', 'address', 'opening_balance', 'active'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
