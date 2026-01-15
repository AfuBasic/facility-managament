<?php

use App\Models\Asset;
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
        Schema::create('work_order_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkOrder::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Asset::class)->constrained()->cascadeOnDelete();
            $table->enum('action', [
                'reserved',
                'consumed',
                'checked_out',
                'checked_in',
                'released',
                'installed'
            ]);
            $table->integer('quantity')->default(1);
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->timestamp('performed_at')->useCurrent();
            $table->text('note')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('work_order_id');
            $table->index('asset_id');
            $table->index('action');
            $table->index('performed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_assets');
    }
};
