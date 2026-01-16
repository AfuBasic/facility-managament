<?php

use App\Models\SlaPolicy;
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
        Schema::table('work_orders', function (Blueprint $table) {
            $table->foreignIdFor(SlaPolicy::class)->nullable()->after('client_account_id')->constrained()->nullOnDelete();
            $table->timestamp('response_due_at')->nullable()->after('sla_policy_id');
            $table->timestamp('resolution_due_at')->nullable()->after('response_due_at');
            $table->timestamp('responded_at')->nullable()->after('resolution_due_at');
            $table->boolean('sla_response_breached')->default(false)->after('responded_at');
            $table->boolean('sla_resolution_breached')->default(false)->after('sla_response_breached');
            $table->timestamp('sla_response_breached_at')->nullable()->after('sla_resolution_breached');
            $table->timestamp('sla_resolution_breached_at')->nullable()->after('sla_response_breached_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeignIdFor(SlaPolicy::class);
            $table->dropColumn([
                'response_due_at',
                'resolution_due_at',
                'responded_at',
                'sla_response_breached',
                'sla_resolution_breached',
                'sla_response_breached_at',
                'sla_resolution_breached_at',
            ]);
        });
    }
};
