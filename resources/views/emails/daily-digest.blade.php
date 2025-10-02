<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>დღიური მიმოხილვა</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 25px;
        }

        .task-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }

        .project-item {
            background: #e8f5e8;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 6px;
        }

        .urgent {
            border-left-color: #dc3545;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>👋 გამარჯობა, {{ $user->name }}!</h1>
        <p>თქვენი დღიური მიმოხილვა {{ $date }}</p>
    </div>

    @if($tasks_due_today->isNotEmpty())
        <div class="section">
            <h2>🚨 დღეს დასასრულებელი დავალებები</h2>
            @foreach($tasks_due_today as $task)
                <div class="task-item urgent">
                    <h4>{{ $task->title }}</h4>
                    <p><strong>პროექტი:</strong> {{ $task->project->name }}</p>
                    <p>{{ Str::limit($task->description) }}</p>
                    <span class="badge badge-warning">დღეს ბოლო ვადაა</span>
                </div>
            @endforeach
        </div>
    @endif

    @if($newly_assigned_tasks->isNotEmpty())
        <div class="section">
            <h2>📋 ახალი დავალებები</h2>
            @foreach($newly_assigned_tasks as $task)
                <div class="task-item">
                    <h4>{{ $task->title }}</h4>
                    <p><strong>პროექტი:</strong> {{ $task->project->name }}</p>
                    <p>{{ Str::limit($task->description) }}</p>
                    <span class="badge badge-info">ახალი დავალება</span>
                </div>
            @endforeach
        </div>
    @endif

    @if($project_updates->isNotEmpty())
        <div class="section">
            <h2>📊 პროექტების განახლებები</h2>
            @foreach($project_updates as $project)
                <div class="project-item">
                    <h4>{{ $project->name }}</h4>
                    <p>{{ Str::limit($project->description, 150) }}</p>
                    <p><small>განახლდა: {{ $project->updated_at->format('d.m.Y H:i') }}</small></p>
                    <p><strong>აქტიური
                            დავალებები:</strong> {{ $project->tasks->where('status', '!=', 'completed')->count() }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <div class="footer">
        <p>იყავით პროდუქტიულები! 🚀</p>
    </div>
</div>
</body>
</html>
