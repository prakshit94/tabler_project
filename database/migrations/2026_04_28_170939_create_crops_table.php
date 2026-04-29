<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('category')->nullable(); // e.g., Cereal, Fruit, Vegetable
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table for Party and Crop
        Schema::create('crop_party', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained()->onDelete('cascade');
            $table->foreignId('crop_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_party');
        Schema::dropIfExists('crops');
    }
};
