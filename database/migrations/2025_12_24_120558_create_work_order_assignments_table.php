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
            $table->foreignIdFor(WorkOrder::class);
            $table->foreignIdFor(User::class,'assigned_by');
            $table->foreignIdFor(User::class,'assigned_to');
            $table->timestamp('assigned_at');
            $table->timestamp('ended_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
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
