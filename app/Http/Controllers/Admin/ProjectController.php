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
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Project::class);
        $projects = Project::all();

        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('admin.projects.create');
    }

    public function store(ProjectRequest $request)
    {
        $this->authorize('create', Project::class);

        $project = new Project($request->validated());
        auth()->user()->projects()->save($project);

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
        $this->authorize('view', $project);

        return view('admin.projects.edit', compact('project'));
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        return back()->with('success', 'პროექტი განახლდა');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return back()->with('success', 'პროექტი წაიშალა');
    }
}
