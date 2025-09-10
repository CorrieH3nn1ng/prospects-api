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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            
            // Basic client information
            $table->string('name');
            $table->string('code')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            
            // Address information
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            
            // Business information
            $table->string('industry')->nullable();
            $table->string('business_type')->nullable();
            $table->integer('employee_count')->nullable();
            $table->decimal('annual_revenue', 15, 2)->nullable();
            
            // Contact information
            $table->string('primary_contact_name')->nullable();
            $table->string('primary_contact_email')->nullable();
            $table->string('primary_contact_phone')->nullable();
            $table->string('primary_contact_designation')->nullable();
            
            // Status and notes
            $table->enum('status', ['active', 'inactive', 'prospect', 'customer'])->default('prospect');
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['company_id', 'branch_id']);
            $table->index(['status', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
