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
        $selectedGroup = $request->query('group');
        $selectedPriority = $request->query('priority');
        $selectedStatus = $request->query('status');

        $tasksQuery = Task::with(['todoList', 'creator'])
            ->orderBy('is_completed')
            ->orderBy('due_date')
            ->latest();

        if ($currentList) {
            $tasksQuery->where('todo_list_id', $currentList->id);
        }

        if ($selectedGroup) {
            $tasksQuery->where('group', $selectedGroup);
        }

        if ($selectedPriority) {
            $priorityNormalized = strtoupper($selectedPriority);
            $priorityMapping = [
                'TINGGI' => 'HIGH',
                'SEDANG' => 'MEDIUM',
                'RENDAH' => 'LOW',
            ];
            $priorityTarget = $priorityMapping[$priorityNormalized] ?? $priorityNormalized;
            $tasksQuery->where(function ($q) use ($priorityTarget, $selectedPriority) {
                $q->where('priority', $priorityTarget)
                    ->orWhere('priority', strtolower($selectedPriority));
            });
        }

        // Base progress calculations across current context
        $baseProgressQuery = Task::query();
        if ($currentList) {
            $baseProgressQuery->where('todo_list_id', $currentList->id);
        }
        $totalTasks = $baseProgressQuery->count();
        $completedTasks = (clone $baseProgressQuery)->where('is_completed', true)->count();
        $progressPercentage = $totalTasks > 0
            ? (int) round(($completedTasks / $totalTasks) * 100)
            : 0;

        // Apply completion status filter if selected (SRS 5)
        if ($selectedStatus === 'active') {
            $tasksQuery->where('is_completed', false);
        } elseif ($selectedStatus === 'completed') {
            $tasksQuery->where('is_completed', true);
        }

        $tasks = $tasksQuery->get();

        $availableLists = TodoList::query()->orderBy('name')->get();

        $baseGroupsQuery = Task::query();
        if ($currentList) {
            $baseGroupsQuery->where('todo_list_id', $currentList->id);
        }
        $availableGroups = $baseGroupsQuery->distinct()->pluck('group')->filter()->values();
        if (! $availableGroups->contains('General')) {
            $availableGroups->prepend('General');
        }

        $groupedTasks = $tasks->groupBy('group');

        return view('tasks', compact(
            'tasks',
            'groupedTasks',
            'totalTasks',
            'completedTasks',
            'progressPercentage',
            'currentList',
            'availableLists',
            'availableGroups',
            'selectedGroup',
            'selectedPriority',
            'selectedStatus'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'group' => ['nullable', 'string', 'max:50'],
            'priority' => ['required', 'in:HIGH,MEDIUM,LOW,high,medium,low,rendah,sedang,tinggi'],
            'due_date' => ['required', 'date'],
            'todo_list_id' => ['nullable', 'exists:todo_lists,id'],
        ]);

        $priorityInput = strtoupper($validated['priority']);
        $priorityMapping = [
            'TINGGI' => 'HIGH',
            'SEDANG' => 'MEDIUM',
            'RENDAH' => 'LOW',
        ];
        $validated['priority'] = $priorityMapping[$priorityInput] ?? $priorityInput;
        $validated['group'] = ! empty($validated['group']) ? trim($validated['group']) : 'General';
        $validated['created_by'] = $request->user()?->id;

        Task::create($validated);

        $redirectParams = [];
        if ($request->filled('todo_list_id')) {
            $redirectParams['list_id'] = $request->input('todo_list_id');
        }

        return redirect()->route('tasks.index', $redirectParams)->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function toggle(Request $request, Task $task): RedirectResponse
    {
        $task->update(['is_completed' => ! $task->is_completed]);

        $redirectParams = [];
        if ($request->filled('list_id')) {
            $redirectParams['list_id'] = $request->input('list_id');
        }
        if ($request->filled('group')) {
            $redirectParams['group'] = $request->input('group');
        }
        if ($request->filled('status')) {
            $redirectParams['status'] = $request->input('status');
        }

        $message = $task->is_completed
            ? "Tugas '{$task->title}' berhasil ditandai selesai!"
            : "Tugas '{$task->title}' dikembalikan ke status belum selesai.";

        return redirect()->route('tasks.index', $redirectParams)->with('success', $message);
    }

    public function listBoard(Request $request, TodoList $todoList): View
    {
        $request->merge(['list_id' => $todoList->id]);

        return $this->index($request);
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'group' => ['nullable', 'string', 'max:50'],
            'priority' => ['required', 'in:HIGH,MEDIUM,LOW,high,medium,low,rendah,sedang,tinggi'],
            'due_date' => ['required', 'date'],
        ]);

        $priorityInput = strtoupper($validated['priority']);
        $priorityMapping = [
            'TINGGI' => 'HIGH',
            'SEDANG' => 'MEDIUM',
            'RENDAH' => 'LOW',
        ];
        $validated['priority'] = $priorityMapping[$priorityInput] ?? $priorityInput;
        $validated['group'] = ! empty($validated['group']) ? trim($validated['group']) : 'General';

        $task->update($validated);

        $redirectParams = [];
        if ($request->filled('list_id') || $task->todo_list_id) {
            $redirectParams['list_id'] = $request->input('list_id', $task->todo_list_id);
        }
        if ($request->filled('group_filter')) {
            $redirectParams['group'] = $request->input('group_filter');
        }

        return redirect()->route('tasks.index', $redirectParams)->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $listId = $request->input('list_id', $task->todo_list_id);
        $task->delete();

        $redirectParams = [];
        if ($listId) {
            $redirectParams['list_id'] = $listId;
        }

        return redirect()->route('tasks.index', $redirectParams)->with('success', 'Tugas berhasil dihapus.');
    }
}
