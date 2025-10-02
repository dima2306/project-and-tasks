<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 02.10.25
 * Time: 14:55
 */

namespace App\Actions\Emails;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PrepareDigestDataAction
{
    public function execute(User $user): array
    {
        $today = Carbon::today();

        return [
            'user' => $user,
            'tasks_due_today' => $this->getTodayDueTasks($user, $today),
            'newly_assigned_tasks' => $this->getNewlyAssignedTasks($user),
            'project_updates' => $this->getProjectUpdates($user),
            'date' => $today->format('Y-m-d'),
        ];
    }

    private function getTodayDueTasks(User $user, Carbon $today): Collection
    {
        return Task::query()
            ->with('project')
            ->whereHas('project', fn(Builder $q) => $q->where('user_id', $user->id))
            ->whereDate('created_at', $today)
            ->where('status', '<>', 'completed')
            ->get();
    }

    private function getNewlyAssignedTasks(User $user): Collection
    {
        return Task::query()
            ->with('project')
            ->whereStatus('completed')
            ->whereHas('project', fn(Builder $q) => $q->where('user_id', $user->id))
            ->where('created_at', '>=', Carbon::yesterday())
            ->get();
    }

    private function getProjectUpdates(User $user): Collection
    {
        return Project::query()
            ->has('tasks')
            ->with('tasks')
            ->where('user_id', $user->id)
            ->where('updated_at', '>=', Carbon::yesterday())
            ->get();
    }
}
