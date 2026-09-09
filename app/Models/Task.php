<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['todo_list_id', 'title', 'priority', 'due_date', 'is_completed'])]
class Task extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'is_completed' => 'boolean',
        ];
    }

    /**
     * Get the todo list that this task belongs to.
     *
     * @return BelongsTo<TodoList, $this>
     */
    public function todoList(): BelongsTo
    {
        return $this->belongsTo(TodoList::class);
    }
}
