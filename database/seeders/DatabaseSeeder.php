<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        \App\Models\Roles::create(['name' => 'Admin']);
        \App\Models\Roles::create(['name' => 'Teacher']);
        \App\Models\Roles::create(['name' => 'Library_staff']);
        \App\Models\Roles::create(['name' => 'Student']);

        \App\Models\User::factory()->create([
            'name' => 'superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('superadmin'),
            'phone' => "1234556667",
            'role_id' => 1
        ]);
    }
}
