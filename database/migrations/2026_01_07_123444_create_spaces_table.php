<?php

use App\Models\ClientAccount;
use App\Models\Facility;
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
        Schema::create('spaces', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ClientAccount::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Facility::class)->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type')->nullable()->comment("e.g., Office, Storage, Meeting Room, etc.");
            $table->string('floor')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spaces');
    }
};
