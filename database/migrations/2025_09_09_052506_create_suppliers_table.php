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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            
            // Basic information
            $table->string('name');
            $table->string('code')->unique(); // Unique supplier code
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            
            // Address information
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            
            // Contact information
            $table->string('contact_person')->nullable();
            
            // Business information
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->json('services_offered')->nullable(); // JSON array of services
            $table->text('payment_terms')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['branch_id']);
            $table->index('code');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
