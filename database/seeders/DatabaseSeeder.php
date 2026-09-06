<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // <--- បន្ថែម Facade នេះ
use App\Models\User;
use App\Models\Roles;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // បង្កើត Roles
        Roles::create(['name' => 'Admin']);
        Roles::create(['name' => 'Teacher']);
        Roles::create(['name' => 'Library_staff']);
        Roles::create(['name' => 'Student']);
        Roles::create(['name' => 'Parent']);

        // បង្កើត Admin User
        User::create([
            'name' => 'superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('superadmin'), // <--- ប្រើ Hash::make
            'phone' => '1234556667',
            'role_id' => 1
        ]);

        \App\Models\User::factory()->create([
            'name' => 'sinh',
            'username' => 'sinhadmin',
            'email' => 'sinhadmin@gmail.com',
            'password' => bcrypt('superadmin'),
            'phone' => "1234556667",
            'role_id' => 2
        ]);

        \App\Models\User::factory()->create([
            'name' => 'staff',
            'username' => 'staff',
            'email' => 'staff@gmail.com',
            'password' => bcrypt('staff'),
            'phone' => "1234556667",
            'role_id' => 3
        ]);

        // បង្កើត Student User
        User::create([
            'name' => 'sinh',
            'username' => 'sinhadmin',
            'email' => 'sinhnaadmin@gmail.com',
            'password' => Hash::make('superadmin'), // <--- ប្រើ Hash::make
            'phone' => '1234556667',
            'role_id' => 4
        ]);

        User::create([
            'name' => 'si',
            'username' => 'siadmin',
            'email' => 'siadmin@gmail.com',
            'password' => Hash::make('superadmin'), // <--- ប្រើ Hash::make
            'phone' => '1234556667888',
            'role_id' => 5
        ]);
    }
}