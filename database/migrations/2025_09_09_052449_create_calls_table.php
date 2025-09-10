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
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            
            // Basic call information
            $table->string('subject');
            $table->text('description')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'follow_up'])->default('scheduled');
            $table->datetime('scheduled_date');
            $table->datetime('planned_date_seen')->nullable();
            $table->datetime('followup_date')->nullable();
            
            // Contact information
            $table->string('contact_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('phone')->nullable();
            $table->string('contact_designation')->nullable();
            $table->string('type_of_business')->nullable();
            
            // Call details
            $table->string('call_type_id')->nullable();
            $table->string('area_of_work')->nullable();
            $table->json('services')->nullable();
            $table->boolean('has_drc_office')->default(false);
            $table->string('inco_terms')->nullable();
            
            // Client assessment
            $table->integer('client_interest_level')->nullable(); // 1-5
            $table->integer('client_mood')->nullable(); // 1-5
            $table->decimal('potential_value', 12, 2)->nullable();
            $table->integer('client_satisfaction_level')->nullable(); // 1-5
            
            // Actions and notes
            $table->json('actions_required')->nullable();
            $table->json('opportunities')->nullable();
            $table->json('routes_challenges')->nullable();
            $table->text('call_notes')->nullable();
            $table->json('documents')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'scheduled_date']);
            $table->index(['user_id', 'status']);
            $table->index(['company_id', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
