<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 30.09.25
 * Time: 00:40
 */

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, Task $task): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Task $task): bool
    {
    }

    public function delete(User $user, Task $task): bool
    {
    }

    public function restore(User $user, Task $task): bool
    {
    }

    public function forceDelete(User $user, Task $task): bool
    {
    }
}
