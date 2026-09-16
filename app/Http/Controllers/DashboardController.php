<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the user dashboard with real Todo List and Task data from database.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Ambil ID semua list milik user atau list yang dibagikan ke user
        $accessibleListIds = TodoList::query()
            ->where('user_id', $user->id)
            ->orWhereHas('members', function ($query) use ($user): void {
                $query->where('users.id', $user->id);
            })
            ->pluck('id');

        $tasksQuery = Task::with('todoList')
            ->where(function ($query) use ($user, $accessibleListIds): void {
                $query->whereIn('todo_list_id', $accessibleListIds)
                    ->orWhere('created_by', $user->id);
            });

        // Fallback untuk development lokal jika list spesifik masih kosong
        if ($tasksQuery->count() === 0 && app()->environment('local', 'testing')) {
            $tasksQuery = Task::with('todoList');
        }

        $allTasks = (clone $tasksQuery)
            ->orderBy('is_completed')
            ->orderBy('due_date')
            ->latest()
            ->get();

        $stats = [
            'total' => $allTasks->count(),
            'completed' => $allTasks->where('is_completed', true)->count(),
            'in_progress' => $allTasks->where('is_completed', false)->where('due_date', '>=', now()->toDateString())->count(),
            'pending' => $allTasks->where('is_completed', false)->count(),
        ];

        $todos = $allTasks;

        return view('dashboard', compact('stats', 'todos', 'user'));
    }
}
