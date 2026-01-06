<?php

use App\Models\ClientAccount;
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
        Schema::create('invitation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ClientAccount::class, 'client_account_id')->constrained()->onDelete('cascade');
            $table->foreignIdFor(User::class)->nullable()->constrained()->onDelete('set null');
            $table->string('email')->index();
            $table->string('role_name');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'password_reset', 'expired'])->default('pending')->index();
            $table->timestamp('invited_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignIdFor(User::class, 'invited_by_user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_new_user')->default(false); // Track if user existed at invitation time
            $table->timestamps();

            // Indexes for common queries
            $table->index(['client_account_id', 'status']);
            $table->index(['email', 'client_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitation_logs');
    }
};
