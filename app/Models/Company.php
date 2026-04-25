<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_name',
        'service_charge_value',
        'vat_charge_value',
        'address',
        'phone',
        'country',
        'message',
        'currency',
    ];
}
