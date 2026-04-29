<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pick_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pick_list_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('stock_batches')->nullOnDelete();
            $table->string('bin_location')->nullable();
            $table->decimal('requested_qty', 15, 2);
            $table->decimal('picked_qty', 15, 2)->default(0);
            $table->string('status')->default('pending'); // pending, picked, partial, skipped
            $table->text('notes')->nullable();
            $table->timestamp('picked_at')->nullable();
            $table->timestamps();

            $table->index(['pick_list_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('pick_list_items'); }
};
