<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::with(['brand', 'category', 'unit'])->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'SKU',
            'Barcode',
            'Brand',
            'Category',
            'Price',
            'Buying Price',
            'Quantity',
            'Alert Quantity',
            'Unit',
            'Status',
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->sku,
            $product->barcode,
            $product->brand?->name,
            $product->category?->name,
            $product->price,
            $product->buying_price,
            $product->qty,
            $product->alert_quantity,
            $product->unit?->name,
            $product->active ? 'Active' : 'Inactive',
        ];
    }
}
