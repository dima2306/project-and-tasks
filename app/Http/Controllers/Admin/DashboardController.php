<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 01.10.25
 * Time: 16:15.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $data = [];

        $data['projects_count'] = Project::count();
        $data['tasks_count'] = Task::count();

        return view('admin.dashboard', $data);
    }
}
