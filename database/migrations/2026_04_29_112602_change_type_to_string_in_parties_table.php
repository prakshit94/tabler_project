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
        Schema::table('parties', function (Blueprint $table) {
            $table->string('type', 50)->default('customer')->change();
            $table->string('category', 50)->default('individual')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->enum('type', ['customer', 'vendor', 'farmer', 'buyer', 'dealer'])->default('customer')->change();
            $table->enum('category', ['individual', 'business'])->default('individual')->change();
        });
    }
};
