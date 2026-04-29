<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('accounting_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('accounting_transactions')->cascadeOnDelete();
            $table->foreignId('ledger_id')->constrained('ledgers')->cascadeOnDelete();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('description')->nullable();
            $table->date('entry_date');
            $table->timestamps();

            $table->index(['transaction_id']);
            $table->index(['ledger_id', 'entry_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('accounting_entries'); }
};
