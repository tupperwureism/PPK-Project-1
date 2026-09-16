<?php

namespace App\Models;

use Database\Factories\TodoListFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'description'])]
class TodoList extends Model
{
    /** @use HasFactory<TodoListFactory> */
    use HasFactory;

    /**
     * Accessor for title to support SRS Dummy Data Contract.
     */
    public function getTitleAttribute(): string
    {
        return $this->name ?? '';
    }

    /**
     * Mutator for title to support SRS Dummy Data Contract.
     */
    public function setTitleAttribute(?string $value): void
    {
        $this->attributes['name'] = $value ?? '';
    }

    /**
     * Get the owner of the todo list.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the members belonging to this todo list.
     *
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'list_members')->withTimestamps();
    }

    /**
     * Get the tasks belonging to this todo list.
     *
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
