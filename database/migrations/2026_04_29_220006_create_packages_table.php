<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_number')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('weight', 10, 3)->nullable(); // kg
            $table->string('dimensions')->nullable(); // LxWxH cm
            $table->string('status')->default('packing'); // packing, packed, shipped
            $table->text('notes')->nullable();
            $table->timestamp('packed_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
        });
    }
    public function down(): void { Schema::dropIfExists('packages'); }
};
