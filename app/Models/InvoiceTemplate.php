<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'layout',
        'header_text',
        'footer_text',
        'show_logo',
        'is_default',
    ];

    protected $casts = [
        'show_logo' => 'boolean',
        'is_default' => 'boolean',
    ];
}
