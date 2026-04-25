<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'price' => 'required|numeric|min:0',
            'buying_price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'alert_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'store_id' => 'nullable|exists:stores,id',
            'unit_id' => 'nullable|exists:units,id',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'active' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
