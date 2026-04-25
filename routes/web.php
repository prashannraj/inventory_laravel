<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockAdjustmentController;

Route::redirect('/', 'login');

Route::get('dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Inventory Management Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Users Management
    Route::resource('users', UserController::class)->middleware('permission:viewUser');
    
    // Brands Management
    Route::resource('brands', BrandController::class)->middleware('permission:viewBrand');
    
    // Categories Management
    Route::resource('categories', CategoryController::class)->middleware('permission:viewCategory');
    
    // Stores Management
    Route::resource('stores', StoreController::class)->middleware('permission:viewStore');
    
    // Attributes Management
    Route::resource('attributes', AttributeController::class)->middleware('permission:viewAttribute');
    Route::get('attributes/{attribute}/values', [AttributeController::class, 'values'])->name('attributes.values');
    Route::post('attributes/{attribute}/values', [AttributeController::class, 'storeValue'])->name('attributes.values.store');
    
    // Units Management
    Route::resource('units', UnitController::class)->middleware('permission:viewUnit');

    // Tax Rates Management
    Route::resource('tax-rates', TaxRateController::class)->middleware('permission:viewTaxRate');

    // Suppliers Management
    Route::resource('suppliers', SupplierController::class)->middleware('permission:viewSupplier');
    
    // Customers Management
    Route::resource('customers', CustomerController::class)->middleware('permission:viewCustomer');

    // Products Management
    Route::resource('products', ProductController::class)->middleware('permission:viewProduct');
    Route::post('products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.stock.update');
    Route::get('products/{product}/movements', [ProductController::class, 'stockMovements'])->name('products.movements');
    
    // Purchases Management
    Route::resource('purchases', PurchaseController::class)->middleware('permission:viewPurchase');
    
    // Sales Management
    Route::resource('sales', SaleController::class)->middleware('permission:viewSale');
    Route::get('sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');
    Route::get('pos', [SaleController::class, 'pos'])->name('sales.pos');
    
    // Stock Adjustments
    Route::resource('stock-adjustments', StockAdjustmentController::class)->middleware('permission:viewAdjustment');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index')->middleware('permission:viewReports');
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('purchases', [ReportController::class, 'purchases'])->name('purchases');
        Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('export/{type}', [ReportController::class, 'export'])->name('export');
    });
    
    // Company Settings
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/', [CompanyController::class, 'edit'])->name('edit')->middleware('permission:updateCompany');
        Route::put('/', [CompanyController::class, 'update'])->name('update')->middleware('permission:updateCompany');
    });
});

require __DIR__.'/auth.php';
