<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'price',
        'buying_price',
        'qty',
        'alert_quantity',
        'image',
        'description',
        'brand_id',
        'category_id',
        'store_id',
        'unit_id',
        'tax_rate_id',
        'active',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getMarginAttribute()
    {
        if ($this->price > 0) {
            return (($this->price - $this->buying_price) / $this->price) * 100;
        }
        return 0;
    }
}
