<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 30.09.25
 * Time: 00:34
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Cache;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $projects = Cache::tags('projects')->remember('projects.listing', 600, function () {
            return Project::all();
        });

        $this->authorize('viewAny', Project::class);

        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        $this->authorize('create', Project::class);

        return view('admin.projects.create');
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $this->authorize('create', Project::class);

        $project = new Project($request->validated());
        auth()->user()->projects()->save($project);

        Cache::tags('projects')->flush();

        return back()->with('success', 'პროექტი შეიქმნა');
    }

    public function show(Project $project): View
    {
        $this->authorize('view', $project);

        $project->load('tasks');

        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        return view('admin.projects.edit', compact('project'));
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        Cache::tags('projects')->flush();

        return back()->with('success', 'პროექტი განახლდა');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        Cache::tags('projects')->flush();

        return back()->with('success', 'პროექტი წაიშალა');
    }
}
