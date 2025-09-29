<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 30.09.25
 * Time: 00:34
 */

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, Project $project): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Project $project): bool
    {
    }

    public function delete(User $user, Project $project): bool
    {
    }

    public function restore(User $user, Project $project): bool
    {
    }

    public function forceDelete(User $user, Project $project): bool
    {
    }
}
