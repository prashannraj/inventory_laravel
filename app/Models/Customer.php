<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'tax_number', 'address', 'opening_balance', 'active'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
