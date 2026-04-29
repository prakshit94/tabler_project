<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('bin_location')->nullable()->after('in_transit_qty');
        });
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->date('manufacture_date')->nullable()->after('expiry_date');
            $table->string('bin_location')->nullable()->after('manufacture_date');
            $table->decimal('reserved_qty', 15, 2)->default(0)->after('qty');
        });
    }
    public function down(): void {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('bin_location');
        });
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->dropColumn(['manufacture_date', 'bin_location', 'reserved_qty']);
        });
    }
};
