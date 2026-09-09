<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Roles;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // បង្កើត Roles
        Roles::firstOrCreate(['name' => 'Admin']);
        Roles::firstOrCreate(['name' => 'Teacher']);
        Roles::firstOrCreate(['name' => 'Library_staff']);
        Roles::firstOrCreate(['name' => 'Student']);
        Roles::firstOrCreate(['name' => 'Parent']);

        // បង្កើត Admin User
        User::firstOrCreate([
            'email' => 'superadmin@gmail.com',
        ], [
            'name' => 'superadmin',
            'username' => 'superadmin',
            'password' => Hash::make('superadmin'),
            'phone' => '1234556667',
            'role_id' => 1
        ]);

        User::firstOrCreate([
            'email' => 'sinhadmin@gmail.com',
        ], [
            'name' => 'sinh',
            'username' => 'sinhadmin',
            'password' => Hash::make('superadmin'),
            'phone' => "1234556668",
            'role_id' => 2
        ]);

        User::firstOrCreate([
            'email' => 'staff@gmail.com',
        ], [
            'name' => 'staff',
            'username' => 'staff',
            'password' => Hash::make('staff'),
            'phone' => "1234556669",
            'role_id' => 3
        ]);

        User::firstOrCreate([
            'email' => 'sinhnaadmin@gmail.com',
        ], [
            'name' => 'sinh',
            'username' => 'sinhstudent',
            'password' => Hash::make('superadmin'),
            'phone' => '1234556670',
            'role_id' => 4
        ]);

        User::firstOrCreate([
            'email' => 'siadmin@gmail.com',
        ], [
            'name' => 'si',
            'username' => 'siadmin',
            'password' => Hash::make('superadmin'),
            'phone' => '123455',
            'role_id' => 5
        ]);

        $this->call(SeedSchoolData::class);
    }
}