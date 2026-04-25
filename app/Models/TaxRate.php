<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $fillable = ['name', 'rate', 'active'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
