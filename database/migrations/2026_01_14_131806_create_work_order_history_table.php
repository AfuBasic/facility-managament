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
        Schema::create('work_order_history', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkOrder::class)->constrained()->cascadeOnDelete();
            $table->string('previous_state')->nullable();
            $table->string('new_state');
            $table->foreignIdFor(User::class, 'changed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('changed_at')->useCurrent();
            $table->text('note')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('work_order_id');
            $table->index('changed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_history');
    }
};
