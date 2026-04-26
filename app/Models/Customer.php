<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'tax_number', 'address', 'opening_balance', 'active', 'credit_limit', 'loyalty_points'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getOutstandingBalanceAttribute()
    {
        return $this->sales()
            ->where('payment_status', '!=', 'paid')
            ->get()
            ->sum(function ($sale) {
                return $sale->net_amount - $sale->paid_amount;
            });
    }

    public function canAfford($amount)
    {
        if ($this->credit_limit <= 0) return true;
        return ($this->outstanding_balance + $amount) <= $this->credit_limit;
    }
}
