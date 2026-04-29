<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('accounting_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->string('type');
            // sales_invoice, payment_received, purchase, purchase_received, cogs, sales_return, purchase_return
            $table->string('reference_type')->nullable(); // Invoice, Payment, Order, Return
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->date('transaction_date');
            $table->string('narration')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            $table->index(['type', 'transaction_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('accounting_transactions'); }
};
