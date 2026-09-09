<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1 Default Admin
        User::firstOrCreate(
            ['email' => 'admin@jara.test'],
            [
                'name' => 'Admin JARA',
                'password' => 'password',
                'role' => 'admin',
                'is_admin' => true,
            ]
        );

        // 2-3 Dummy Regular Users for Testing
        User::firstOrCreate(
            ['email' => 'user1@jara.test'],
            [
                'name' => 'User JARA Satu',
                'password' => 'password',
                'role' => 'user',
                'is_admin' => false,
            ]
        );

        User::firstOrCreate(
            ['email' => 'user2@jara.test'],
            [
                'name' => 'User JARA Dua',
                'password' => 'password',
                'role' => 'user',
                'is_admin' => false,
            ]
        );

        User::firstOrCreate(
            ['email' => 'user3@jara.test'],
            [
                'name' => 'User JARA Tiga',
                'password' => 'password',
                'role' => 'user',
                'is_admin' => false,
            ]
        );
    }
}
