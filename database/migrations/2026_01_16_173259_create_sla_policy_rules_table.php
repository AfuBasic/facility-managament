<?php

use App\Models\SlaPolicy;
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
        Schema::create('sla_policy_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SlaPolicy::class)->constrained()->cascadeOnDelete();
            $table->enum('priority', ['low', 'medium', 'high', 'critical']);
            $table->integer('response_time_minutes');
            $table->integer('resolution_time_minutes');
            $table->timestamps();

            $table->unique(['sla_policy_id', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sla_policy_rules');
    }
};
