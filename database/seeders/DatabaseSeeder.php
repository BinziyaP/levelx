<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Admin User (Specific)
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'role' => 'admin',
                'password' => bcrypt('Admin@123'),
            ]
        );

        // Seller User (Example)
        \App\Models\User::factory()->create([
            'name' => 'Seller User',
            'email' => 'seller@example.com',
            'role' => 'seller',
            'password' => bcrypt('password'),
        ]);

        // Buyer User
        \App\Models\User::factory()->create([
            'name' => 'Buyer User',
            'email' => 'buyer@example.com',
            'role' => 'buyer',
            'password' => bcrypt('password'),
        ]);
    }
}
