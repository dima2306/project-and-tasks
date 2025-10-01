<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 30.09.25
 * Time: 00:40
 */

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view tasks', Task::class);
    }

    public function view(User $user, Task $task): bool
    {
        return $user->can('view', $task->project) || $user->hasRole(['admin', 'user']);
    }

    public function create(User $user, ?Project $project = null): bool
    {
        if ($project) {
            return $user->can('update', $project);
        }

        return $user->hasRole('admin');
    }

    public function update(User $user, Task $task): bool
    {
        // Can update task if can update parent project
        return $user->can('update', $task->project);
    }

    public function delete(User $user, Task $task): bool
    {
        // Can update task if can update parent project
        return $user->can('update', $task->project);
    }
}
