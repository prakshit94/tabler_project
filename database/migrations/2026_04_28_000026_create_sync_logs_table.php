<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->enum('direction', ['in', 'out']);
            $table->longText('payload')->nullable();
            $table->longText('response')->nullable();
            $table->string('status');
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('sync_logs'); }
};
