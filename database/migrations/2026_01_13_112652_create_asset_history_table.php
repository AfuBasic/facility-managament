<?php

use App\Models\Asset;
use App\Models\Space;
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
        Schema::create('asset_history', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Asset::class)->constrained()->onDelete('cascade');
            $table->enum('action_type', ['restock', 'checkout', 'checkin', 'maintenance', 'audit', 'transfer']);
            $table->foreignIdFor(User::class, 'performed_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignIdFor(User::class, 'target_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignIdFor(Space::class)->nullable()->constrained()->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('cost_per_unit', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->json('previous_state')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['asset_id', 'action_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_history');
    }
};
