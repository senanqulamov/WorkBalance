<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Seed test users for development and testing.
     */
    public function run(): void
    {
        // Create admin/buyer user
        User::factory()->create([
            'name' => 'Buyer Admin',
            'email' => 'buyer@dpanel.test',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'is_buyer' => true,
            'is_seller' => false,
            'is_supplier' => false,
            'company_name' => 'DPanel Corp',
            'is_active' => true,
        ]);

        // Create supplier users
        User::factory()->supplier()->create([
            'name' => 'Supplier One',
            'email' => 'supplier1@dpanel.test',
            'password' => Hash::make('password'),
            'company_name' => 'Supplier Corp',
            'is_active' => true,
        ]);

        User::factory()->supplier()->create([
            'name' => 'Supplier Two',
            'email' => 'supplier2@dpanel.test',
            'password' => Hash::make('password'),
            'company_name' => 'Supplier Industries',
            'is_active' => true,
        ]);

        // Create seller user
        User::factory()->seller()->create([
            'name' => 'Seller One',
            'email' => 'seller@dpanel.test',
            'password' => Hash::make('password'),
            'company_name' => 'Seller LLC',
            'is_active' => true,
        ]);

        $this->command->info('Test users created successfully!');
        $this->command->info('---');
        $this->command->info('Buyer: buyer@dpanel.test / password');
        $this->command->info('Supplier 1: supplier1@dpanel.test / password');
        $this->command->info('Supplier 2: supplier2@dpanel.test / password');
        $this->command->info('Seller: seller@dpanel.test / password');
    }
}
