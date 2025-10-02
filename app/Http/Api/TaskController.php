<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 02.10.25
 * Time: 13:37.
 */

namespace App\Http\Api;

use App\Actions\Tasks\MarkTaskAsCompletedAction;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Task $task, MarkTaskAsCompletedAction $action): JsonResponse
    {
        $this->authorize('update', $task);

        return $action->execute($task);
    }
}
