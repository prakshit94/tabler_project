<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('carrier')->nullable(); // DHL, FedEx, India Post, etc.
            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->string('status')->default('pending');
            // pending, dispatched, in_transit, out_for_delivery, delivered, returned, failed
            $table->date('estimated_delivery')->nullable();
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index('tracking_number');
        });
    }
    public function down(): void { Schema::dropIfExists('shipments'); }
};
