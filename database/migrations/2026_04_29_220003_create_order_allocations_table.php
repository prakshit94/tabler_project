<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('order_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('stock_batches')->nullOnDelete();
            $table->string('bin_location')->nullable();
            $table->decimal('allocated_qty', 15, 2);
            $table->string('status')->default('allocated'); // allocated, picked, cancelled
            $table->timestamps();

            $table->index(['order_id', 'order_item_id']);
            $table->index(['product_id', 'warehouse_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('order_allocations'); }
};
