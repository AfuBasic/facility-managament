<?php

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
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('space_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('quantity')->default(1);
            $table->timestamp('checked_out_at')->useCurrent();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index for quick lookup of active assignments per user or asset
            $table->index(['asset_id', 'user_id']);
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
