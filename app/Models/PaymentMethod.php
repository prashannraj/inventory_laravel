<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'gateway',
        'details',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'details' => 'array',
    ];

    public function getDisplayDetailsAttribute()
    {
        $details = $this->details;
        if (is_array($details)) {
            return 'Configured';
        }
        if (is_string($details) && $details !== '') {
            return $details;
        }
        // fallback to raw original value
        $raw = $this->getRawOriginal('details') ?? '';
        return is_string($raw) ? $raw : '';
    }
}
