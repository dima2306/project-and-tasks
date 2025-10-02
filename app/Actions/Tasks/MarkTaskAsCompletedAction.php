<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 02.10.25
 * Time: 13:34
 */

namespace App\Actions\Tasks;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class MarkTaskAsCompletedAction
{
    public function execute(Task $task): JsonResponse|RedirectResponse
    {
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'დავალება წარმატებით დასრულდა',
                'task' => $task->fresh(['project']),
            ]);
        }

        return to_route('admin.tasks.show', $task)
            ->with('success', 'დავალება წარმატებით დასრულდა');
    }
}
