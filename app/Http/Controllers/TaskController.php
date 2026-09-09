<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(): View
    {
        $tasks = Task::query()
            ->orderBy('is_completed')
            ->orderBy('due_date')
            ->latest()
            ->get();
        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('is_completed', true)->count();
        $progressPercentage = $totalTasks > 0
            ? (int) round(($completedTasks / $totalTasks) * 100)
            : 0;

        return view('tasks', compact('tasks', 'totalTasks', 'completedTasks', 'progressPercentage'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:rendah,sedang,tinggi'],
            'due_date' => ['required', 'date'],
        ]);

        Task::create($validated);

        return to_route('tasks.index')->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function toggle(Task $task): RedirectResponse
    {
        $task->update(['is_completed' => ! $task->is_completed]);

        return to_route('tasks.index');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return to_route('tasks.index')->with('success', 'Tugas berhasil dihapus.');
    }
}
