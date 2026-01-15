<?php

use App\Models\Asset;
use App\Models\ClientAccount;
use App\Models\Facility;
use App\Models\User;
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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            
            // Core fields
            $table->foreignIdFor(ClientAccount::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Facility::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Asset::class)->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            
            // Status tracking
            $table->enum('status', [
                'reported',
                'approved',
                'rejected',
                'assigned',
                'in_progress',
                'on_hold',
                'completed',
                'closed'
            ])->default('reported');
            
            // Reporting
            $table->foreignIdFor(User::class, 'reported_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('reported_at')->useCurrent();
            
            // Approval/Rejection
            $table->foreignIdFor(User::class, 'approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_note')->nullable();
            
            $table->foreignIdFor(User::class, 'rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Assignment
            $table->foreignIdFor(User::class, 'assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignIdFor(User::class, 'assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->text('assignment_note')->nullable();
            
            // Execution
            $table->foreignIdFor(User::class, 'started_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            
            $table->foreignIdFor(User::class, 'paused_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paused_at')->nullable();
            $table->text('pause_reason')->nullable();
            
            $table->foreignIdFor(User::class, 'resumed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resumed_at')->nullable();
            
            // Completion
            $table->foreignIdFor(User::class, 'completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->text('completion_notes')->nullable();
            $table->integer('time_spent')->nullable()->comment('Time in minutes');
            $table->decimal('total_cost', 10, 2)->nullable();
            
            // Closure
            $table->foreignIdFor(User::class, 'closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->text('closure_note')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('priority');
            $table->index('reported_at');
            $table->index(['client_account_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
