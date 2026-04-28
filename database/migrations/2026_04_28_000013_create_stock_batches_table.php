<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('batch_no');
            $table->date('expiry_date')->nullable();
            $table->decimal('qty', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['product_id', 'warehouse_id', 'batch_no']);
        });
    }
    public function down(): void { Schema::dropIfExists('stock_batches'); }
};
