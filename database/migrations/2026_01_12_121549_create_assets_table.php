<?php

use App\Models\ClientAccount;
use App\Models\Contact;
use App\Models\Facility;
use App\Models\Space;
use App\Models\Store;
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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ClientAccount::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Facility::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Store::class)->nullable()->constrained()->onDelete('set null');
            $table->foreignIdFor(User::class)->nullable()->constrained()->onDelete('set null');
            $table->string('serial');
            $table->integer('units')->default(1);
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignIdFor(Contact::class, 'supplier_contact_id')->nullable()->constrained('contacts')->onDelete('set null');
            $table->enum('type', ['fixed', 'tools', 'consumable']);
            $table->integer('minimum')->default(0);
            $table->integer('maximum')->default(0);
            $table->date('purchased_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignIdFor(Space::class)->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['client_account_id', 'facility_id']);
            $table->index('type');
            $table->unique(['client_account_id', 'serial']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
