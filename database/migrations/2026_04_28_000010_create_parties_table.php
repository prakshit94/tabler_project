<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            
            // System Identifiers
            $table->uuid('uuid')->unique();
            $table->string('party_code')->unique(); // CUST-000001 or VEND-000001

            // Identity
            $table->string('name'); // Combined display name
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mobile', 20)->unique()->index();
            $table->string('email')->nullable()->index();
            $table->string('phone_number_2', 20)->nullable();
            $table->string('relative_phone', 20)->nullable();

            // Source & Reference
            $table->string('source', 50)->nullable();
            $table->string('referred_by')->nullable();

            // Classification
            $table->enum('type', ['customer', 'vendor', 'farmer', 'buyer', 'dealer'])->default('customer');
            $table->enum('category', ['individual', 'business'])->default('individual');

            // Business Details
            $table->string('company_name')->nullable();
            $table->string('gstin')->nullable()->index();
            $table->string('pan_number')->nullable();

            // Agriculture Profile (Mostly for Farmers)
            $table->decimal('land_area', 10, 2)->nullable();
            $table->string('land_unit')->default('acre');
            $table->json('crops')->nullable(); 
            $table->string('irrigation_type')->nullable(); // borewell, canal, rainfed

            // Financial / Credit
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->decimal('outstanding_balance', 15, 2)->default(0);
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->date('credit_valid_till')->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('ledger_group')->nullable();

            // Bank Details
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('branch_name')->nullable();

            // KYC & Compliance
            $table->string('aadhaar_last4')->nullable();
            $table->boolean('kyc_completed')->default(false);
            $table->timestamp('kyc_verified_at')->nullable();

            // Engagement
            $table->date('first_purchase_at')->nullable();
            $table->date('last_purchase_at')->nullable();
            $table->unsignedInteger('orders_count')->default(0);

            // Status & Control
            $table->boolean('is_active')->default(true);
            $table->boolean('is_blacklisted')->default(false);
            $table->text('internal_notes')->nullable();
            $table->json('tags')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['type', 'is_active']);
        });
    }
    public function down(): void { Schema::dropIfExists('parties'); }
};
