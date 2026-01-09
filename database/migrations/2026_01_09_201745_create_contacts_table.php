<?php

use App\Models\ClientAccount;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\ContactType;
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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ClientAccount::class)->constrained()->onDelete('cascade');
            $table->string('firstname');
            $table->string('lastname');
            $table->foreignIdFor(ContactType::class)->nullable()->constrained()->onDelete('set null');
            $table->foreignIdFor(ContactGroup::class)->nullable()->constrained()->onDelete('set null');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('email')->nullable();
            $table->string('phone');
            $table->date('birthday')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->foreignIdFor(Contact::class)->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            $table->index(['contact_type_id', 'contact_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
