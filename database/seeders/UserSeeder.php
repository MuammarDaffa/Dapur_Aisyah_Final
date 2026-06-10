<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@dapuraisyah.com'],
            [
                'name' => 'Admin Dapur Aisyah',
                'phone' => '081234567890',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Owner
        User::updateOrCreate(
            ['email' => 'owner@dapuraisyah.com'],
            [
                'name' => 'Owner Dapur Aisyah',
                'phone' => '081234567891',
                'password' => bcrypt('password'),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]
        );

        // Sample Customer
        User::updateOrCreate(
            ['email' => 'pelanggan@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'phone' => '082345678901',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );
    }
}
