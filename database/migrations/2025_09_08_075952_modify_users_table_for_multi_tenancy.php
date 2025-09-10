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
        Schema::table('users', function (Blueprint $table) {
            // Add multi-tenancy fields
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            
            // Add role-based permissions
            $table->enum('role', ['app_admin', 'company_admin', 'branch_user'])->default('branch_user');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            
            // Add additional user fields
            $table->string('phone')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->json('permissions')->nullable(); // Additional granular permissions
            $table->json('settings')->nullable(); // User-specific settings
            
            // Add indexes for performance
            $table->index(['company_id', 'status']);
            $table->index(['branch_id', 'status']);
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['company_id', 'status']);
            $table->dropIndex(['branch_id', 'status']);
            $table->dropIndex(['role']);
            
            // Drop foreign key constraints
            $table->dropForeign(['company_id']);
            $table->dropForeign(['branch_id']);
            
            // Drop columns
            $table->dropColumn([
                'company_id',
                'branch_id',
                'role',
                'status',
                'phone',
                'last_login_at',
                'permissions',
                'settings'
            ]);
        });
    }
};
