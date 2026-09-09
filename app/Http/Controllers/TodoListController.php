<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TodoListController extends Controller
{
    /**
     * Resolve the currently authenticated user, with local development fallback.
     */
    protected function resolveUser(Request $request): User
    {
        $user = $request->user();

        if (! $user && app()->environment('local', 'testing')) {
            $user = User::first() ?? User::factory()->create([
                'name' => 'Demo User',
                'email' => 'demo@jara.test',
            ]);
            auth()->login($user);
        }

        if (! $user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        return $user;
    }

    /**
     * Display a listing of todo lists for the current user.
     */
    public function index(Request $request): View
    {
        $user = $this->resolveUser($request);

        $ownedLists = $user->todoLists()
            ->with(['members'])
            ->latest()
            ->get();

        $sharedLists = $user->sharedTodoLists()
            ->with(['user', 'members'])
            ->latest()
            ->get();

        $availableUsers = User::query()
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        return view('lists', compact('user', 'ownedLists', 'sharedLists', 'availableUsers'));
    }

    /**
     * Store a newly created todo list in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $this->resolveUser($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama list tugas wajib diisi.',
            'name.max' => 'Nama list tugas maksimal 255 karakter.',
        ]);

        $user->todoLists()->create([
            'name' => $validated['name'],
        ]);

        return redirect()->route('lists.index')->with('success', 'Daftar tugas baru berhasil dibuat.');
    }

    /**
     * Remove the specified todo list from storage.
     */
    public function destroy(Request $request, TodoList $todoList): RedirectResponse
    {
        $user = $this->resolveUser($request);

        if ($todoList->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus list ini.');
        }

        $todoList->delete();

        return redirect()->route('lists.index')->with('success', 'Daftar tugas berhasil dihapus.');
    }

    /**
     * Add another user as a member to the specified todo list.
     */
    public function addMember(Request $request, TodoList $todoList): RedirectResponse
    {
        $user = $this->resolveUser($request);

        if ($todoList->user_id !== $user->id) {
            abort(403, 'Hanya pemilik list yang dapat menambahkan anggota.');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id', 'different:'.$user->id],
        ], [
            'user_id.required' => 'Pilih teman/pengguna yang ingin ditambahkan.',
            'user_id.exists' => 'Pengguna tidak ditemukan dalam sistem.',
            'user_id.different' => 'Anda tidak dapat menambahkan diri sendiri sebagai anggota kolaborasi.',
        ]);

        $todoList->members()->syncWithoutDetaching([$validated['user_id']]);

        return redirect()->route('lists.index')->with('success', 'Anggota kolaborasi berhasil ditambahkan ke list tugas.');
    }
}
