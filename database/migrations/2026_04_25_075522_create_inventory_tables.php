<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Stores table (warehouses)
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Attributes table (product attributes like color, size)
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Attribute values table (specific values for attributes)
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Products table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('qty')->default(0);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Product attribute pivot table (many-to-many with attribute values)
        Schema::create('product_attribute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('attribute_value_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Orders table
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no')->unique();
            $table->string('customer_name');
            $table->string('customer_address');
            $table->string('customer_phone');
            $table->dateTime('date_time');
            $table->decimal('gross_amount', 10, 2);
            $table->decimal('service_charge_rate', 5, 2)->default(0);
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->decimal('vat_charge_rate', 5, 2)->default(0);
            $table->decimal('vat_charge', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->enum('paid_status', ['paid', 'unpaid', 'partial'])->default('unpaid');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Order items table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('qty');
            $table->decimal('rate', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        // Company settings table
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->decimal('service_charge_value', 5, 2);
            $table->string('vat_charge_value');
            $table->string('address');
            $table->string('phone');
            $table->string('country');
            $table->string('message');
            $table->string('currency');
            $table->timestamps();
        });

        // Stock movements table (for stock ledger)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['in', 'out', 'adjustment', 'transfer']);
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reference_id')->nullable()->comment('Reference to order, purchase, etc.');
            $table->string('reference_type')->nullable()->comment('Order, Purchase, Adjustment, etc.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product_attribute');
        Schema::dropIfExists('products');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('stores');
    }
};
