<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->string('group')->default('general'); // general, tally, inventory, accounting
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        DB::table('system_settings')->insert([
            ['key' => 'tally_sync_mode', 'value' => 'manual', 'type' => 'string', 'group' => 'tally', 'label' => 'Tally Sync Mode', 'description' => 'manual, scheduled, or instant', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tally_url', 'value' => 'http://localhost:9000', 'type' => 'string', 'group' => 'tally', 'label' => 'Tally URL', 'description' => 'Tally server XML import URL', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tally_company', 'value' => '', 'type' => 'string', 'group' => 'tally', 'label' => 'Tally Company Name', 'description' => 'Company name in Tally', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tally_max_retries', 'value' => '3', 'type' => 'integer', 'group' => 'tally', 'label' => 'Max Retry Count', 'description' => 'Max retries on failure', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'low_stock_threshold', 'value' => '10', 'type' => 'integer', 'group' => 'inventory', 'label' => 'Low Stock Alert Threshold', 'description' => 'Alert when stock below this qty', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'allocation_method', 'value' => 'fifo', 'type' => 'string', 'group' => 'inventory', 'label' => 'Batch Allocation Method', 'description' => 'fifo or fefo', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
    public function down(): void { Schema::dropIfExists('system_settings'); }
};
