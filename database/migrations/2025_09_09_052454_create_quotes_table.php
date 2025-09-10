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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('call_id')->nullable()->constrained()->onDelete('set null');
            
            // Quote information
            $table->string('quote_number')->unique();
            $table->string('subject');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'sent', 'pending', 'accepted', 'rejected', 'expired'])->default('draft');
            
            // Dates
            $table->date('quote_date');
            $table->date('valid_until');
            $table->date('accepted_date')->nullable();
            
            // Financial information
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            
            // Terms and conditions
            $table->text('terms_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->string('inco_terms')->nullable();
            
            // Quote items (stored as JSON for simplicity)
            $table->json('items')->nullable();
            
            // Client feedback
            $table->text('client_feedback')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'quote_date']);
            $table->index(['user_id', 'status']);
            $table->index(['company_id', 'branch_id']);
            $table->index(['client_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
