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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('business_registration')->nullable();
            $table->string('tax_number')->nullable();
            $table->enum('subscription_plan', ['basic', 'standard', 'premium', 'enterprise'])->default('basic');
            $table->enum('status', ['active', 'suspended', 'cancelled'])->default('active');
            $table->timestamp('subscription_starts_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->json('settings')->nullable(); // Store company-specific settings
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
