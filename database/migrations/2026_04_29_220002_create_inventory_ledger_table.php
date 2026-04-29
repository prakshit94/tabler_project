<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('stock_batches')->nullOnDelete();
            $table->string('type'); // reserve, release, commit, ship, deliver, return, adjust, purchase_receive
            $table->decimal('quantity', 15, 2); // positive = in, negative = out
            $table->decimal('balance_after', 15, 2); // on_hand balance after this entry
            $table->string('reference_type')->nullable(); // Order, Return, StockTransfer, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['product_id', 'warehouse_id']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('type');
        });
    }
    public function down(): void { Schema::dropIfExists('inventory_ledger'); }
};
