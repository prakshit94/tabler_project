<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('returns', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete()->after('party_id');
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete()->after('warehouse_id');
            $table->string('qc_status')->default('pending')->after('status');
            // pending, in_progress, passed, failed
            $table->string('disposition')->nullable()->after('qc_status');
            // restock, scrap, replace
            $table->text('qc_notes')->nullable()->after('disposition');
            $table->timestamp('pickup_at')->nullable()->after('qc_notes');
            $table->timestamp('received_at')->nullable()->after('pickup_at');
            $table->timestamp('qc_at')->nullable()->after('received_at');
            $table->timestamp('restocked_at')->nullable()->after('qc_at');
        });
    }
    public function down(): void {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['inspector_id']);
            $table->dropColumn([
                'warehouse_id', 'inspector_id', 'qc_status', 'disposition',
                'qc_notes', 'pickup_at', 'received_at', 'qc_at', 'restocked_at',
            ]);
        });
    }
};
