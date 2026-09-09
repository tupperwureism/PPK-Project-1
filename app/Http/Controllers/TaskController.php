<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $listId = $request->query('list_id');
        $currentList = $listId ? TodoList::find($listId) : null;

        $tasksQuery = Task::with('todoList')
            ->orderBy('is_completed')
            ->orderBy('due_date')
            ->latest();

        if ($currentList) {
            $tasksQuery->where('todo_list_id', $currentList->id);
        }

        $tasks = $tasksQuery->get();
        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('is_completed', true)->count();
        $progressPercentage = $totalTasks > 0
            ? (int) round(($completedTasks / $totalTasks) * 100)
            : 0;

        $availableLists = TodoList::query()->orderBy('name')->get();

        return view('tasks', compact(
            'tasks',
            'totalTasks',
            'completedTasks',
            'progressPercentage',
            'currentList',
            'availableLists'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:rendah,sedang,tinggi'],
            'due_date' => ['required', 'date'],
            'todo_list_id' => ['nullable', 'exists:todo_lists,id'],
        ]);

        Task::create($validated);

        $redirectTarget = $request->filled('todo_list_id')
            ? route('tasks.index', ['list_id' => $request->input('todo_list_id')])
            : route('tasks.index');

        return redirect($redirectTarget)->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function toggle(Request $request, Task $task): RedirectResponse
    {
        $task->update(['is_completed' => ! $task->is_completed]);

        $redirectTarget = $request->filled('list_id')
            ? route('tasks.index', ['list_id' => $request->input('list_id')])
            : route('tasks.index');

        return redirect($redirectTarget);
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $listId = $request->input('list_id', $task->todo_list_id);
        $task->delete();

        $redirectTarget = $listId
            ? route('tasks.index', ['list_id' => $listId])
            : route('tasks.index');

        return redirect($redirectTarget)->with('success', 'Tugas berhasil dihapus.');
    }
}
