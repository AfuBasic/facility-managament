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
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Asset::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Space::class)->nullable()->constrained()->onDelete('set null');
            $table->integer('quantity')->default(1);
            $table->timestamp('checked_out_at')->useCurrent();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for quick lookups
            $table->index(['asset_id', 'user_id']);
            $table->index('checked_out_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
