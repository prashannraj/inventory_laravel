<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'active'];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
