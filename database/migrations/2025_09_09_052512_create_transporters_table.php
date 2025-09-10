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
        Schema::create('transporters', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            
            // Basic information
            $table->string('name');
            $table->string('code')->unique(); // Unique transporter code
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            
            // Address information
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            
            // Contact information
            $table->string('contact_person')->nullable();
            
            // Transport-specific fields
            $table->enum('transport_type', ['road', 'rail', 'air', 'sea', 'multimodal'])->default('road');
            $table->decimal('vehicle_capacity', 10, 2)->nullable(); // in tons or cubic meters
            $table->string('capacity_unit')->default('tons'); // tons, cubic_meters, pallets
            $table->text('coverage_area')->nullable(); // Geographic coverage description
            $table->json('licensing_info')->nullable(); // JSON for various licenses and certifications
            
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
            $table->index('transport_type');
            $table->index(['transport_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transporters');
    }
};
