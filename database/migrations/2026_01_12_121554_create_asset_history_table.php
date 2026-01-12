<?php

use App\Models\Asset;
use App\Models\Store;
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
            $table->enum('status', ['check_in', 'check_out', 'repairs'])->nullable();
            $table->foreignIdFor(User::class,'performed_by_user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action_type')->index()->comment('restock, checkout, checkin, audit, maintenance');
            $table->foreignIdFor(User::class, 'target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('cost_per_unit', 10, 2)->nullable();
            $table->json('previous_state')->nullable();
            $table->integer('units')->default(1);
            $table->text('note')->nullable();
            $table->foreignIdFor(Store::class, 'to_store')->nullable()->constrained('stores')->onDelete('set null');
            $table->timestamps();

            // Indexes for better query performance
            $table->index('asset_id');
            $table->index(['asset_id', 'status']);
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
