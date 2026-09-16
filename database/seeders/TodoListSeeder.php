<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Database\Seeder;

class TodoListSeeder extends Seeder
{
    /**
     * Run the database seeds sesuai Dummy Data Contract (Section 11 Blueprint).
     */
    public function run(): void
    {
        $budi = User::where('email', 'budi@jara.app')->first();
        $siti = User::where('email', 'siti@jara.app')->first();

        if (! $budi) {
            $budi = User::create([
                'name' => 'budi_dev',
                'email' => 'budi@jara.app',
                'password' => 'password',
                'role' => 'user',
                'is_admin' => false,
            ]);
        }

        if (! $siti) {
            $siti = User::create([
                'name' => 'siti_qa',
                'email' => 'siti@jara.app',
                'password' => 'password',
                'role' => 'user',
                'is_admin' => false,
            ]);
        }

        // List: Pengembangan Sprint 1 (owner: budi_dev)
        $list = TodoList::firstOrCreate(
            [
                'user_id' => $budi->id,
                'name' => 'Pengembangan Sprint 1',
            ]
        );

        // Collaborator Member: siti_qa
        $list->members()->syncWithoutDetaching([$siti->id]);

        // Task: Setup database prepared statements
        Task::firstOrCreate(
            [
                'todo_list_id' => $list->id,
                'title' => 'Setup database prepared statements',
            ],
            [
                'priority' => 'tinggi',
                'due_date' => '2026-09-20',
                'is_completed' => false,
            ]
        );
    }
}
