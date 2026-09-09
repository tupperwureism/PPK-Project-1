<?php

namespace Database\Seeders;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Database\Seeder;

class TodoListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $users = User::factory()->count(3)->create();
        }

        $user1 = $users->first();
        $user2 = $users->skip(1)->first() ?? User::factory()->create();

        $listKuliah = TodoList::firstOrCreate([
            'user_id' => $user1->id,
            'name' => 'Tugas Kuliah',
        ]);

        $listProjek = TodoList::firstOrCreate([
            'user_id' => $user1->id,
            'name' => 'Projek Tim',
        ]);

        $listProjek->members()->syncWithoutDetaching([$user2->id]);
    }
}
