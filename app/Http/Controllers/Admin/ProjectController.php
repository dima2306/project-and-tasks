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

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Project::class);
        $projects = Project::all();

        return view('admin.projects.index', compact('projects'));
    }

    public function store(ProjectRequest $request)
    {
        $this->authorize('create', Project::class);

        return Project::create($request->validated());
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return $project;
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        return $project;
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json();
    }
}
