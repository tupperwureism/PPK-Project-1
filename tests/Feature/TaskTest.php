<?php

namespace Tests\Feature;

use App\Models\Task;
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
