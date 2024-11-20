<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
            'full_name' => 'sofia',
            'username' => 'fia',
            'email' => 'sofia@gmail.com',
            'password' => bcrypt('12345'),
            'phone_number' => '0899766666',
            'role' => 'Admin',
            'is_active' => 1,
            'image' => ''
        ]);
    }
}
