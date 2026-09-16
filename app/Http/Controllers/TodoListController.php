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
            ->with(['members', 'tasks'])
            ->latest()
            ->get();

        $sharedLists = $user->sharedTodoLists()
            ->with(['user', 'members', 'tasks'])
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
     * Display the specified todo list board with progress tracking and task list.
     */
    public function show(Request $request, TodoList $todoList): View
    {
        $user = $this->resolveUser($request);

        // Otorisasi: Pemilik atau Anggota Kolaborasi
        $isOwner = $todoList->user_id === $user->id;
        $isMember = $todoList->members()->where('users.id', $user->id)->exists();

        if (! $isOwner && ! $isMember) {
            abort(403, 'Anda tidak memiliki hak akses ke daftar tugas ini.');
        }

        // Eager load relasi
        $todoList->load([
            'user',
            'members',
            'tasks' => function ($query): void {
                $query->orderBy('is_completed')->orderBy('due_date')->latest('id');
            },
        ]);

        // Progress Calculation Engine (Developer 3)
        $totalTasks = $todoList->tasks->count();
        $completedTasks = $todoList->tasks->where('is_completed', true)->count();
        $progressPercentage = $totalTasks > 0
            ? (int) round(($completedTasks / $totalTasks) * 100)
            : 0;

        $availableUsers = User::query()
            ->where('id', '!=', $user->id)
            ->where('id', '!=', $todoList->user_id)
            ->whereNotIn('id', $todoList->members->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('lists.show', compact(
            'user',
            'todoList',
            'isOwner',
            'totalTasks',
            'completedTasks',
            'progressPercentage',
            'availableUsers'
        ));
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
