<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Display the specified todo list detail page.
     */
    public function show(Request $request, TodoList $todoList): View
    {
        $user = $this->resolveUser($request);

        $isOwner = $todoList->user_id === $user->id;
        $isMember = $todoList->members()->where('users.id', $user->id)->exists();

        if (! $isOwner && ! $isMember) {
            abort(403, 'Anda tidak memiliki akses ke list tugas ini.');
        }

        $todoList->load(['user', 'members', 'tasks']);

        $availableUsers = User::query()
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        return view('lists.show', compact('user', 'todoList', 'availableUsers', 'isOwner'));
    }

    /**
     * Store a newly created todo list in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $this->resolveUser($request);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'name.max' => 'Nama list tugas maksimal 255 karakter.',
            'title.max' => 'Judul list tugas maksimal 255 karakter.',
        ]);

        $listName = $validated['title'] ?? $validated['name'] ?? null;
        if (empty($listName)) {
            return back()->withErrors(['name' => 'Nama atau judul list tugas wajib diisi.'])->withInput();
        }

        $user->todoLists()->create([
            'name' => $listName,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('lists.index')->with('success', 'Daftar tugas baru berhasil dibuat.');
    }

    /**
     * Remove the specified todo list from storage with atomic DB transaction.
     */
    public function destroy(Request $request, TodoList $todoList): RedirectResponse
    {
        $user = $this->resolveUser($request);

        if ($todoList->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus list ini.');
        }

        DB::transaction(function () use ($todoList): void {
            $todoList->tasks()->delete();
            $todoList->members()->detach();
            $todoList->delete();
        });

        return redirect()->route('lists.index')->with('success', 'Daftar tugas beserta seluruh tugas terkait berhasil dihapus secara atomik.');
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
            'user_id' => ['nullable', 'exists:users,id', 'different:'.$user->id],
            'email' => ['nullable', 'email', 'exists:users,email'],
        ], [
            'user_id.exists' => 'Pengguna tidak ditemukan dalam sistem.',
            'user_id.different' => 'Anda tidak dapat menambahkan diri sendiri sebagai anggota kolaborasi.',
            'email.exists' => 'Pengguna dengan email tersebut tidak ditemukan.',
        ]);

        $memberId = $validated['user_id'] ?? null;
        if (! $memberId && ! empty($validated['email'])) {
            $memberUser = User::where('email', $validated['email'])->first();
            if ($memberUser) {
                if ($memberUser->id === $user->id) {
                    return redirect()->back(fallback: route('lists.index'))->withErrors(['email' => 'Anda tidak dapat menambahkan diri sendiri sebagai anggota kolaborasi.']);
                }
                $memberId = $memberUser->id;
            }
        }

        if (! $memberId) {
            return redirect()->back(fallback: route('lists.index'))->withErrors(['user_id' => 'Pilih pengguna atau masukkan email yang valid.']);
        }

        $todoList->members()->syncWithoutDetaching([$memberId]);

        return redirect()->back(fallback: route('lists.index'))->with('success', 'Anggota kolaborasi berhasil ditambahkan ke list tugas.');
    }
}
