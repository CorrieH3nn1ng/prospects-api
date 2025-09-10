<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin company
        $company = Company::firstOrCreate(
            ['email' => 'admin@prospects.com'],
            [
                'name' => 'System Admin Company',
                'email' => 'admin@prospects.com',
                'subscription_plan' => 'enterprise',
                'status' => 'active'
            ]
        );

        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@prospects.com'],
            [
                'name' => 'System Admin',
                'email' => 'admin@prospects.com',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'email_verified_at' => now()
            ]
        );

        $this->command->info('Admin user created: admin@prospects.com / password');
    }
}
