<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Store;
use App\Models\Unit;
use App\Models\TaxRate;
use App\Models\ProductImage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['brand', 'category', 'unit'])->latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $brands = Brand::where('active', true)->get();
        $categories = Category::where('active', true)->get();
        $stores = Store::where('active', true)->get();
        $units = Unit::where('active', true)->get();
        $taxRates = TaxRate::where('active', true)->get();
        
        return view('products.create', compact('brands', 'categories', 'stores', 'units', 'taxRates'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        
        // Auto-generate SKU if not provided
        if (empty($data['sku'])) {
            $data['sku'] = 'PRD-' . strtoupper(Str::random(8));
        }
        
        // Auto-generate Barcode if not provided
        if (empty($data['barcode'])) {
            $data['barcode'] = time() . mt_rand(1000, 9999);
        }

        $product = Product::create($data);

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['brand', 'category', 'unit', 'taxRate', 'images', 'stockMovements']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $brands = Brand::where('active', true)->get();
        $categories = Category::where('active', true)->get();
        $stores = Store::where('active', true)->get();
        $units = Unit::where('active', true)->get();
        $taxRates = TaxRate::where('active', true)->get();
        
        return view('products.edit', compact('product', 'brands', 'categories', 'stores', 'units', 'taxRates'));
    }

    public function update(Request $request, Product $product)
    {
        // For simplicity using Request instead of UpdateProductRequest for now to handle unique SKU/Barcode
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'required|string|max:100|unique:products,barcode,' . $product->id,
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
        ]);

        $product->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => false,
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Delete images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
        
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
