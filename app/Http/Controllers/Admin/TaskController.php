<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 30.09.25
 * Time: 00:40
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Cache;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Task::class);

        $tasks = Cache::tags('tasks')->remember('tasks.listing', 600, function () {
            return Task::all();
        });

        return view('admin.tasks.index', compact('tasks'));
    }

    public function store(TaskRequest $request)
    {
        $this->authorize('create', Task::class);

        Cache::tags('tasks')->flush();

        return Task::create($request->validated());
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return $task;
    }

    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        Cache::tags('tasks')->flush();

        return $task;
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        Cache::tags('tasks')->flush();

        return response()->json();
    }
}
