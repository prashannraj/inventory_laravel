<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['name'])) {
            return null;
        }

        $brand = null;
        if (!empty($row['brand'])) {
            $brand = Brand::firstOrCreate(['name' => $row['brand']]);
        }

        $category = null;
        if (!empty($row['category'])) {
            $category = Category::firstOrCreate(['name' => $row['category']]);
        }

        $unit = null;
        if (!empty($row['unit'])) {
            $unit = Unit::firstOrCreate(['name' => $row['unit'], 'short_name' => strtolower($row['unit'])]);
        }

        $sku = $row['sku'];
        
        if ($sku) {
            $product = Product::where('sku', $sku)->first();
            if ($product) {
                $product->update([
                    'name'           => $row['name'],
                    'barcode'        => $row['barcode'] ?? $product->barcode,
                    'brand_id'       => $brand?->id ?? $product->brand_id,
                    'category_id'    => $category?->id ?? $product->category_id,
                    'unit_id'        => $unit?->id ?? $product->unit_id,
                    'price'          => $row['price'] ?? $product->price,
                    'buying_price'   => $row['buying_price'] ?? $product->buying_price,
                    'qty'            => $row['quantity'] ?? $product->qty,
                    'alert_quantity' => $row['alert_quantity'] ?? $product->alert_quantity,
                    'active'         => isset($row['status']) ? (strtolower($row['status']) === 'active') : $product->active,
                ]);
                return null; // Skip creating as we updated
            }
        }

        return new Product([
            'name'           => $row['name'],
            'sku'            => $sku ?? 'PRD-' . strtoupper(Str::random(8)),
            'barcode'        => $row['barcode'] ?? time() . mt_rand(1000, 9999),
            'brand_id'       => $brand?->id,
            'category_id'    => $category?->id,
            'unit_id'        => $unit?->id,
            'price'          => $row['price'] ?? 0,
            'buying_price'   => $row['buying_price'] ?? 0,
            'qty'            => $row['quantity'] ?? 0,
            'alert_quantity' => $row['alert_quantity'] ?? 5,
            'active'         => (strtolower($row['status'] ?? 'active') === 'active'),
        ]);
    }
}
