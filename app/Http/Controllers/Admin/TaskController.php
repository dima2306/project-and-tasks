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
use App\Models\Project;
use App\Models\Task;
use Cache;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public const array TASK_STATUSES = [
        'todo' => 'გასაკეთებელია',
        'in_progress' => 'მიმდინარეობს',
    ];

    public function index(): View
    {
        $this->authorize('viewAny', Task::class);

        $tasks = Cache::tags('tasks')->remember('tasks.listing', 600, function () {
            return Task::all();
        });

        return view('admin.tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        $data['projects'] = Project::all();
        $data['statuses'] = self::TASK_STATUSES;

        return view('admin.tasks.create', $data);
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        $this->authorize('create', Task::class);

        Task::create($request->validated());

        Cache::tags('tasks')->flush();

        return back()->with('success', 'დავალება შეიქმნა');
    }

    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        return view('admin.tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        $data['task'] = $task;
        $data['projects'] = Project::all();
        $data['statuses'] = self::TASK_STATUSES;

        return view('admin.tasks.edit', $data);
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        Cache::tags('tasks')->flush();

        return back()->with('success', 'დავალება განახლდა');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        Cache::tags('tasks')->flush();

        return back()->with('success', 'დავალება წაიშალა');
    }
}
