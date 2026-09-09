<?php

namespace Tests\Feature;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * FR-06: Pengguna dapat melihat daftar list miliknya.
     */
    public function test_user_can_view_todo_lists_page(): void
    {
        $user = User::factory()->create();
        $list = TodoList::factory()->create([
            'user_id' => $user->id,
            'name' => 'Projek Praktikum',
        ]);

        $response = $this->actingAs($user)->get(route('lists.index'));

        $response->assertOk();
        $response->assertSee('Projek Praktikum');
        $response->assertSee('👑 Pemilik');
    }

    /**
     * FR-05: Pengguna dapat membuat list/kategori tugas baru.
     */
    public function test_user_can_create_new_todo_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('lists.store'), [
            'name' => 'Tugas Kuliah',
        ]);

        $response->assertRedirect(route('lists.index'));
        $this->assertDatabaseHas('todo_lists', [
            'user_id' => $user->id,
            'name' => 'Tugas Kuliah',
        ]);
    }

    /**
     * FR-05 Validation: Nama list wajib diisi.
     */
    public function test_creating_todo_list_requires_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('lists.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseCount('todo_lists', 0);
    }

    /**
     * FR-07: Pemilik list dapat menghapus list tugas miliknya.
     */
    public function test_owner_can_delete_their_todo_list(): void
    {
        $user = User::factory()->create();
        $list = TodoList::factory()->create([
            'user_id' => $user->id,
            'name' => 'List untuk Dihapus',
        ]);

        $response = $this->actingAs($user)->delete(route('lists.destroy', $list));

        $response->assertRedirect(route('lists.index'));
        $this->assertDatabaseMissing('todo_lists', [
            'id' => $list->id,
        ]);
    }

    /**
     * FR-07 Security: Pengguna lain tidak dapat menghapus list yang bukan miliknya.
     */
    public function test_non_owner_cannot_delete_todo_list(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $list = TodoList::factory()->create([
            'user_id' => $owner->id,
            'name' => 'List Rahasia',
        ]);

        $response = $this->actingAs($stranger)->delete(route('lists.destroy', $list));

        $response->assertForbidden();
        $this->assertDatabaseHas('todo_lists', [
            'id' => $list->id,
        ]);
    }

    /**
     * FR-08: Pemilik list dapat menambahkan teman/user lain ke dalam list.
     */
    public function test_owner_can_add_member_to_todo_list(): void
    {
        $owner = User::factory()->create();
        $friend = User::factory()->create();
        $list = TodoList::factory()->create([
            'user_id' => $owner->id,
            'name' => 'Projek Kolaborasi',
        ]);

        $response = $this->actingAs($owner)->post(route('lists.add-member', $list), [
            'user_id' => $friend->id,
        ]);

        $response->assertRedirect(route('lists.index'));
        $this->assertDatabaseHas('list_members', [
            'todo_list_id' => $list->id,
            'user_id' => $friend->id,
        ]);
    }

    /**
     * FR-08 Security: Selain pemilik tidak dapat menambahkan anggota.
     */
    public function test_non_owner_cannot_add_member_to_todo_list(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $friend = User::factory()->create();
        $list = TodoList::factory()->create([
            'user_id' => $owner->id,
            'name' => 'Projek Pribadi',
        ]);

        $response = $this->actingAs($stranger)->post(route('lists.add-member', $list), [
            'user_id' => $friend->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('list_members', [
            'todo_list_id' => $list->id,
            'user_id' => $friend->id,
        ]);
    }

    /**
     * Integritas Data: Menghapus list otomatis menghapus data relasi anggota (cascade delete).
     */
    public function test_deleting_list_cascades_delete_on_member_relations(): void
    {
        $owner = User::factory()->create();
        $friend = User::factory()->create();
        $list = TodoList::factory()->create(['user_id' => $owner->id]);
        $list->members()->attach($friend->id);

        $this->assertDatabaseHas('list_members', [
            'todo_list_id' => $list->id,
            'user_id' => $friend->id,
        ]);

        $this->actingAs($owner)->delete(route('lists.destroy', $list));

        $this->assertDatabaseMissing('todo_lists', ['id' => $list->id]);
        $this->assertDatabaseMissing('list_members', ['todo_list_id' => $list->id]);
    }
}
