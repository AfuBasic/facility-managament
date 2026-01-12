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
        Schema::create('image_cache', function (Blueprint $table) {
            $table->id();
            $table->string('image_hash', 64)->unique(); // SHA-256 hash of the image file
            $table->string('url'); // Cloudinary URL
            $table->string('secure_url'); // Cloudinary secure URL
            $table->string('public_id'); // Cloudinary public ID for deletion
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('format', 10)->nullable();
            $table->integer('file_size')->nullable(); // File size in bytes
            $table->integer('usage_count')->default(1); // Track how many times this image is used
            $table->timestamps();

            // Index for fast hash lookups
            $table->index('image_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_cache');
    }
};
