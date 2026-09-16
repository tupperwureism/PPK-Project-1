<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressTrackingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * REQ-06: Owner dapat mengakses halaman board list dan melihat progress tracker.
     */
    public function test_owner_can_access_list_board_and_see_progress_tracker(): void
    {
        $owner = User::factory()->create();
        $list = TodoList::factory()->create(['user_id' => $owner->id, 'name' => 'Sprint Proyek']);

        $response = $this->actingAs($owner)->get(route('lists.show', $list));

        $response->assertOk();
        $response->assertSee('Sprint Proyek');
        $response->assertSee('👑 Pemilik List');
        $response->assertSee('Progres Penyelesaian Tugas');
    }

    /**
     * REQ-06: Anggota kolaborasi dapat mengakses board list dan melihat progress tracker.
     */
    public function test_collaborator_member_can_access_list_board_and_see_progress_tracker(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $list = TodoList::factory()->create(['user_id' => $owner->id, 'name' => 'Projek Bersama']);
        $list->members()->attach($member->id);

        $response = $this->actingAs($member)->get(route('lists.show', $list));

        $response->assertOk();
        $response->assertSee('Projek Bersama');
        $response->assertSee('🤝 Anggota Kolaborasi');
        $response->assertSee('Progres Penyelesaian Tugas');
    }

    /**
     * Security / Otorisasi: Pengguna lain di luar list ditolak (403 Forbidden).
     */
    public function test_stranger_cannot_access_list_board(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $list = TodoList::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($stranger)->get(route('lists.show', $list));

        $response->assertForbidden();
    }

    /**
     * Progress Calculation Engine: Perhitungan metrik ketika belum ada tugas (0%).
     */
    public function test_progress_calculation_with_zero_tasks(): void
    {
        $owner = User::factory()->create();
        $list = TodoList::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)->get(route('lists.show', $list));

        $response->assertOk();
        $response->assertSee('0%');
        $response->assertSee('0');
        $response->assertSee('dari');
        $response->assertSee('Belum Ada Tugas');
    }

    /**
     * Progress Calculation Engine: Perhitungan rasio dan persentase sebagian (3/5 = 60%).
     */
    public function test_progress_calculation_with_partial_completed_tasks(): void
    {
        $owner = User::factory()->create();
        $list = TodoList::factory()->create(['user_id' => $owner->id]);

        // Buat 3 tugas selesai dan 2 tugas belum selesai
        Task::create([
            'todo_list_id' => $list->id,
            'title' => 'Tugas Selesai 1',
            'priority' => 'tinggi',
            'due_date' => now()->toDateString(),
            'is_completed' => true,
        ]);
        Task::create([
            'todo_list_id' => $list->id,
            'title' => 'Tugas Selesai 2',
            'priority' => 'sedang',
            'due_date' => now()->toDateString(),
            'is_completed' => true,
        ]);
        Task::create([
            'todo_list_id' => $list->id,
            'title' => 'Tugas Selesai 3',
            'priority' => 'rendah',
            'due_date' => now()->toDateString(),
            'is_completed' => true,
        ]);
        Task::create([
            'todo_list_id' => $list->id,
            'title' => 'Tugas Pending 1',
            'priority' => 'tinggi',
            'due_date' => now()->toDateString(),
            'is_completed' => false,
        ]);
        Task::create([
            'todo_list_id' => $list->id,
            'title' => 'Tugas Pending 2',
            'priority' => 'sedang',
            'due_date' => now()->toDateString(),
            'is_completed' => false,
        ]);

        $response = $this->actingAs($owner)->get(route('lists.show', $list));

        $response->assertOk();
        $response->assertSee('60%');
        $response->assertSee('3');
        $response->assertSee('5');
        $response->assertSee('Dalam Progres');
    }

    /**
     * Progress Calculation Engine: Perhitungan metrik ketika seluruh tugas selesai (100%).
     */
    public function test_progress_calculation_with_all_completed_tasks(): void
    {
        $owner = User::factory()->create();
        $list = TodoList::factory()->create(['user_id' => $owner->id]);

        Task::create([
            'todo_list_id' => $list->id,
            'title' => 'Tugas A',
            'priority' => 'tinggi',
            'due_date' => now()->toDateString(),
            'is_completed' => true,
        ]);
        Task::create([
            'todo_list_id' => $list->id,
            'title' => 'Tugas B',
            'priority' => 'sedang',
            'due_date' => now()->toDateString(),
            'is_completed' => true,
        ]);

        $response = $this->actingAs($owner)->get(route('lists.show', $list));

        $response->assertOk();
        $response->assertSee('100%');
        $response->assertSee('2');
        $response->assertSee('Selesai');
    }
}
