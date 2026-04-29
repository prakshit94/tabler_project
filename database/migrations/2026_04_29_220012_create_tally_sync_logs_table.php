<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tally_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('reference_type'); // Invoice, Payment, Purchase, SalesReturn, PurchaseReturn
            $table->unsignedBigInteger('reference_id');
            $table->string('voucher_type');
            // sales_voucher, receipt_voucher, purchase_voucher, credit_note, debit_note
            $table->longText('payload')->nullable(); // XML payload sent to Tally
            $table->string('status')->default('pending'); // pending, success, failed
            $table->unsignedSmallInteger('retry_count')->default(0);
            $table->longText('response')->nullable(); // Tally response
            $table->string('error_message')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            $table->index(['status', 'retry_count']);
        });
    }
    public function down(): void { Schema::dropIfExists('tally_sync_logs'); }
};
