<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds sesuai Dummy Data Contract (Section 11 Blueprint).
     */
    public function run(): void
    {
        // 1. Admin (usr-1)
        User::firstOrCreate(
            ['email' => 'admin@jara.app'],
            [
                'name' => 'admin1',
                'password' => 'password',
                'role' => 'admin',
                'is_admin' => true,
            ]
        );

        // 2. User / Owner (usr-2)
        User::firstOrCreate(
            ['email' => 'budi@jara.app'],
            [
                'name' => 'budi_dev',
                'password' => 'password',
                'role' => 'user',
                'is_admin' => false,
            ]
        );

        // 3. User / Member (usr-3)
        User::firstOrCreate(
            ['email' => 'siti@jara.app'],
            [
                'name' => 'siti_qa',
                'password' => 'password',
                'role' => 'user',
                'is_admin' => false,
            ]
        );
    }
}
