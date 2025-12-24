<?php

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
            $table->foreignIdFor(Facility::class);
            $table->foreignIdFor(ClientAccount::class);
            $table->foreignIdFor(User::class, 'opened_by');
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['pending', 'in_progress','in_review', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
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
