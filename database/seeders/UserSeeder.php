<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test company
        $company = Company::create([
            'name' => 'Test Company Ltd',
            'email' => 'admin@testcompany.com',
            'phone' => '+1 234 567 8900',
            'address' => '123 Business St, City, State 12345',
            'website' => 'https://testcompany.com',
            'business_registration' => 'REG123456789',
            'tax_number' => 'TAX987654321',
            'subscription_plan' => 'premium',
            'status' => 'active',
            'subscription_starts_at' => now(),
            'subscription_ends_at' => now()->addYear(),
            'monthly_fee' => 299.99,
        ]);

        // Create a test branch
        $branch = Branch::create([
            'company_id' => $company->id,
            'name' => 'Main Office',
            'code' => 'MAIN001',
            'email' => 'main@testcompany.com',
            'phone' => '+1 234 567 8901',
            'address' => '123 Business St, City, State 12345',
            'manager_name' => 'Jane Manager',
            'status' => 'active',
        ]);

        // Create test users
        
        // App Admin
        User::create([
            'name' => 'App Administrator',
            'email' => 'admin@prospects.com',
            'password' => Hash::make('password123'),
            'role' => 'app_admin',
            'status' => 'active',
            'phone' => '+1 234 567 8888',
        ]);

        // Company Admin
        User::create([
            'name' => 'Company Admin',
            'email' => 'company@testcompany.com',
            'password' => Hash::make('password123'),
            'company_id' => $company->id,
            'role' => 'company_admin',
            'status' => 'active',
            'phone' => '+1 234 567 8999',
        ]);

        // Branch User
        User::create([
            'name' => 'Branch User',
            'email' => 'user@testcompany.com',
            'password' => Hash::make('password123'),
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'role' => 'branch_user',
            'status' => 'active',
            'phone' => '+1 234 567 9000',
        ]);
    }
}
