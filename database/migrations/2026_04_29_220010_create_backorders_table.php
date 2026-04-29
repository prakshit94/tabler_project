<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('backorders', function (Blueprint $table) {
            $table->id();
            $table->string('backorder_number')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->decimal('pending_qty', 15, 2);
            $table->decimal('fulfilled_qty', 15, 2)->default(0);
            $table->string('status')->default('pending');
            // pending, waiting_stock, allocated, fulfilled, cancelled
            $table->text('notes')->nullable();
            $table->timestamp('expected_date')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'product_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('backorders'); }
};
