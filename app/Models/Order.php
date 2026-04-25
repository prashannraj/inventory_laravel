<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'bill_no',
        'customer_name',
        'customer_address',
        'customer_phone',
        'date_time',
        'gross_amount',
        'service_charge_rate',
        'service_charge',
        'vat_charge_rate',
        'vat_charge',
        'net_amount',
        'discount',
        'paid_status',
        'user_id',
    ];

    protected $casts = [
        'date_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
