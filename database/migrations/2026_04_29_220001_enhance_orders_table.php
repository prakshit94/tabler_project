<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('priority')->default('normal')->after('status'); // normal, high, urgent
            $table->text('notes')->nullable()->after('priority');
            $table->string('current_warehouse_id')->nullable()->after('notes'); // allocated warehouse
            $table->timestamp('confirmed_at')->nullable()->after('current_warehouse_id');
            $table->timestamp('allocated_at')->nullable()->after('confirmed_at');
            $table->timestamp('picking_at')->nullable()->after('allocated_at');
            $table->timestamp('picked_at')->nullable()->after('picking_at');
            $table->timestamp('packing_at')->nullable()->after('picked_at');
            $table->timestamp('packed_at')->nullable()->after('packing_at');
            $table->timestamp('shipped_at')->nullable()->after('packed_at');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            $table->timestamp('closed_at')->nullable()->after('delivered_at');
            $table->timestamp('cancelled_at')->nullable()->after('closed_at');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('cancelled_at');
        });
    }
    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'priority', 'notes', 'current_warehouse_id',
                'confirmed_at', 'allocated_at', 'picking_at', 'picked_at',
                'packing_at', 'packed_at', 'shipped_at', 'delivered_at',
                'closed_at', 'cancelled_at', 'created_by',
            ]);
        });
    }
};
