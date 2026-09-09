<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_tasks_page_displays_progress(): void
    {
        Task::create([
            'title' => 'Selesai',
            'priority' => 'tinggi',
            'due_date' => '2026-09-10',
            'is_completed' => true,
        ]);
        Task::create([
            'title' => 'Belum selesai',
            'priority' => 'rendah',
            'due_date' => '2026-09-11',
        ]);

        $this->get('/tasks')
            ->assertOk()
            ->assertSee('50%')
            ->assertSee('Selesai')
            ->assertSee('Belum selesai');
    }

    public function test_tasks_page_can_filter_by_todo_list(): void
    {
        $user = User::factory()->create();
        $listA = TodoList::factory()->create(['user_id' => $user->id, 'name' => 'Projek Web']);
        $listB = TodoList::factory()->create(['user_id' => $user->id, 'name' => 'Tugas Matematika']);

        Task::create([
            'todo_list_id' => $listA->id,
            'title' => 'Desain Wireframe Web',
            'priority' => 'tinggi',
            'due_date' => '2026-09-15',
        ]);
        Task::create([
            'todo_list_id' => $listB->id,
            'title' => 'Kalkulus Bab 3',
            'priority' => 'rendah',
            'due_date' => '2026-09-16',
        ]);

        $response = $this->get('/tasks?list_id='.$listA->id);

        $response->assertOk()
            ->assertSee('Projek Web')
            ->assertSee('Desain Wireframe Web')
            ->assertDontSee('Kalkulus Bab 3');
    }

    public function test_task_can_be_created(): void
    {
        $this->post('/tasks', [
            'title' => 'Siapkan presentasi',
            'priority' => 'tinggi',
            'due_date' => '2026-09-15',
        ])->assertRedirect('/tasks');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Siapkan presentasi',
            'priority' => 'tinggi',
            'is_completed' => false,
        ]);
    }

    public function test_task_can_be_created_with_todo_list_id(): void
    {
        $user = User::factory()->create();
        $list = TodoList::factory()->create(['user_id' => $user->id, 'name' => 'Projek Praktikum']);

        $response = $this->post('/tasks', [
            'todo_list_id' => $list->id,
            'title' => 'Testing Modul Integrasi',
            'priority' => 'tinggi',
            'due_date' => '2026-09-12',
        ]);

        $response->assertRedirect('/tasks?list_id='.$list->id);

        $this->assertDatabaseHas('tasks', [
            'todo_list_id' => $list->id,
            'title' => 'Testing Modul Integrasi',
            'priority' => 'tinggi',
            'is_completed' => false,
        ]);
    }

    public function test_task_can_be_toggled_and_deleted(): void
    {
        $task = Task::create([
            'title' => 'Tugas tim',
            'priority' => 'sedang',
            'due_date' => '2026-09-20',
        ]);

        $this->patch("/tasks/{$task->id}/toggle")->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'is_completed' => true]);

        $this->delete("/tasks/{$task->id}")->assertRedirect('/tasks');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_task_creation_requires_valid_fields(): void
    {
        $this->from('/tasks')->post('/tasks', [
            'title' => '',
            'priority' => 'urgent',
            'due_date' => 'not-a-date',
        ])->assertRedirect('/tasks');

        $this->assertDatabaseCount('tasks', 0);
    }
}
