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
            'priority' => 'HIGH',
            'group' => 'General',
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
            'priority' => 'HIGH',
            'group' => 'General',
            'is_completed' => false,
        ]);
    }

    public function test_task_can_be_created_with_custom_group_and_high_priority(): void
    {
        $response = $this->post('/tasks', [
            'title' => 'Setup database prepared statements',
            'group' => 'Backend',
            'priority' => 'HIGH',
            'due_date' => '2026-09-20',
        ]);

        $response->assertRedirect('/tasks');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Setup database prepared statements',
            'group' => 'Backend',
            'priority' => 'HIGH',
            'is_completed' => false,
        ]);
    }

    public function test_tasks_can_be_filtered_by_group(): void
    {
        Task::create([
            'title' => 'Tugas Backend API',
            'group' => 'Backend',
            'priority' => 'HIGH',
            'due_date' => '2026-09-25',
        ]);
        Task::create([
            'title' => 'Tugas Frontend UI',
            'group' => 'Frontend',
            'priority' => 'LOW',
            'due_date' => '2026-09-26',
        ]);

        $response = $this->get('/tasks?group=Backend');

        $response->assertOk()
            ->assertSee('Tugas Backend API')
            ->assertDontSee('Tugas Frontend UI');
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

    public function test_list_board_can_be_accessed_via_lists_show(): void
    {
        $user = User::factory()->create();
        $list = TodoList::factory()->create(['user_id' => $user->id, 'name' => 'Sprint 1 JARA']);

        Task::create([
            'todo_list_id' => $list->id,
            'title' => 'Tugas Spesifik List',
            'priority' => 'HIGH',
            'due_date' => '2026-09-30',
        ]);

        $response = $this->get('/lists/'.$list->id);

        $response->assertOk()
            ->assertSee('Sprint 1 JARA')
            ->assertSee('Tugas Spesifik List');
    }

    public function test_task_can_be_updated_full_crud(): void
    {
        $task = Task::create([
            'title' => 'Judul Lama',
            'group' => 'OldGroup',
            'priority' => 'LOW',
            'due_date' => '2026-09-20',
        ]);

        $response = $this->put('/tasks/'.$task->id, [
            'title' => 'Judul Baru Diperbarui',
            'group' => 'Backend',
            'priority' => 'HIGH',
            'due_date' => '2026-09-28',
        ]);

        $response->assertRedirect('/tasks');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Judul Baru Diperbarui',
            'group' => 'Backend',
            'priority' => 'HIGH',
        ]);
    }

    public function test_task_toggle_flashes_feedback_and_updates_status(): void
    {
        $task = Task::create([
            'title' => 'Tugas Review Sprint',
            'priority' => 'HIGH',
            'due_date' => '2026-09-25',
            'is_completed' => false,
        ]);

        $response = $this->patch("/tasks/{$task->id}/toggle");

        $response->assertRedirect('/tasks');
        $response->assertSessionHas('success', "Tugas 'Tugas Review Sprint' berhasil ditandai selesai!");
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'is_completed' => true]);

        // Toggle kembali (undo)
        $revertResponse = $this->patch("/tasks/{$task->id}/toggle");
        $revertResponse->assertRedirect('/tasks');
        $revertResponse->assertSessionHas('success', "Tugas 'Tugas Review Sprint' dikembalikan ke status belum selesai.");
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'is_completed' => false]);
    }

    public function test_tasks_can_be_filtered_by_completion_status(): void
    {
        Task::create([
            'title' => 'Tugas Masih Berjalan',
            'priority' => 'HIGH',
            'due_date' => '2026-09-25',
            'is_completed' => false,
        ]);
        Task::create([
            'title' => 'Tugas Sudah Rampung',
            'priority' => 'LOW',
            'due_date' => '2026-09-26',
            'is_completed' => true,
        ]);

        // Filter active (belum selesai)
        $this->get('/tasks?status=active')
            ->assertOk()
            ->assertSee('Tugas Masih Berjalan')
            ->assertDontSee('Tugas Sudah Rampung');

        // Filter completed (selesai)
        $this->get('/tasks?status=completed')
            ->assertOk()
            ->assertSee('Tugas Sudah Rampung')
            ->assertDontSee('Tugas Masih Berjalan');
    }
}
