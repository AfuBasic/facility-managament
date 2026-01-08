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
        Schema::create('facility_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ClientAccount::class);
            $table->string('designation');
            $table->foreignIdFor(Facility::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('removed_at')->nullable();
            $table->timestamps();
            
            // Ensure a user can only be assigned once to a facility (if not removed)
            $table->unique(['facility_id', 'user_id', 'removed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_user');
    }
};
