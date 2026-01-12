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
        Schema::table('assets', function (Blueprint $table) {
            $table->timestamp('checked_out_at')->nullable()->after('assigned_to_user_id');
            $table->timestamp('last_checked_in_at')->nullable()->after('checked_out_at');
        });

        Schema::table('asset_history', function (Blueprint $table) {
             $table->foreignId('space_id')->nullable()->after('target_user_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_history', function (Blueprint $table) {
            $table->dropForeign(['space_id']);
            $table->dropColumn('space_id');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['checked_out_at', 'last_checked_in_at']);
        });
    }
};
