<?php

use App\Models\User;
use App\Models\WorkOrder;
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
        Schema::create('work_order_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkOrder::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'assigned_to')->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'assigned_by')->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'unassigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at');
            $table->timestamp('unassigned_at')->nullable();
            $table->string('assignment_note')->nullable();
            $table->string('unassignment_reason')->nullable();
            $table->boolean('is_current')->default(true);
            $table->timestamps();

            $table->index(['work_order_id', 'is_current']);
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_assignments');
    }
};
