<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->foreignId('tax_rate_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hsn_code_id')->nullable()->constrained()->nullOnDelete();
            $table->string('unit')->default('NOS');
            $table->boolean('is_active')->default(true);
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('barcode')->nullable()->index();
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('mrp', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->integer('min_stock_level')->default(0);
            $table->boolean('batch_tracking')->default(false);
            $table->boolean('expiry_tracking')->default(false);
            $table->foreignId('default_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('sku');
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void { 
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products'); 
    }
};
