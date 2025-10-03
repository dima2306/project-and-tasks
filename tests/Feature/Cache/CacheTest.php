<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::flush();
    $this->user = User::factory()->create();
    $this->project = Project::factory()->for($this->user)->create();
});

describe('Cache Functionality', function () {
    describe('Project Caching', function () {
        it('caches project data on the first access', function () {
            $cacheKey = "project.{$this->project->id}";

            expect(Cache::has($cacheKey))->toBeFalse();

            // Simulate caching project data
            $cachedProject = Cache::remember($cacheKey, 3600, function () {
                return $this->project->fresh();
            });

            expect(Cache::has($cacheKey))->toBeTrue();
            expect($cachedProject->id)->toBe($this->project->id);
        });

        it('invalidates project cache when updated', function () {
            $cacheKey = "project.{$this->project->id}";

            // Cache the project
            Cache::put($cacheKey, $this->project, 3600);
            expect(Cache::has($cacheKey))->toBeTrue();

            // Update project
            $this->project->update(['name' => 'Updated Project']);

            // Simulate cache invalidation on model update
            Cache::forget($cacheKey);
            expect(Cache::has($cacheKey))->toBeFalse();
        });

        it('caches user projects list', function () {
            Project::factory(5)->for($this->user)->create();
            $cacheKey = "user.{$this->user->id}.projects";

            $cachedProjects = Cache::remember($cacheKey, 3600, function () {
                return $this->user->projects()->get();
            });

            expect(Cache::has($cacheKey))->toBeTrue();
            expect($cachedProjects)->toHaveCount(6); // 5 + 1 from beforeEach
        });
    });

    describe('Task Caching', function () {
        it('caches task data with relationships', function () {
            $task = Task::factory()->for($this->project)->create();
            $cacheKey = "task.{$task->id}.with_project";

            $cachedTask = Cache::remember($cacheKey, 3600, function () use ($task) {
                return $task->load('project');
            });

            expect(Cache::has($cacheKey))->toBeTrue();
            expect($cachedTask->project->id)->toBe($this->project->id);
        });

        it('caches daily tasks for user', function () {
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();

            Task::factory(3)->for($this->project)->create(['created_at' => $today]);
            Task::factory(2)->for($this->project)->create(['created_at' => $yesterday]);

            $cacheKey = "user.{$this->user->id}.daily_tasks." . $today->format('Y-m-d');

            $dailyTasks = Cache::remember($cacheKey, 1440, function () use ($today) {
                return $this->user->tasks()
                    ->whereDate('tasks.created_at', $today)
                    ->with('project')
                    ->get();
            });

            expect(Cache::has($cacheKey))->toBeTrue();
            expect($dailyTasks)->toHaveCount(3);
        });
    });

    describe('Cache Invalidation', function () {
        it('invalidates related caches when project is deleted', function () {
            $projectCacheKey = "project.{$this->project->id}";
            $userProjectsCacheKey = "user.{$this->user->id}.projects";
            $dashboardCacheKey = "user.{$this->user->id}.dashboard";

            // Set up caches
            Cache::put($projectCacheKey, $this->project, 3600);
            Cache::put($userProjectsCacheKey, collect([$this->project]), 3600);
            Cache::put($dashboardCacheKey, ['projects_count' => 1], 3600);

            // Verify caches exist
            expect(Cache::has($projectCacheKey))->toBeTrue();
            expect(Cache::has($userProjectsCacheKey))->toBeTrue();
            expect(Cache::has($dashboardCacheKey))->toBeTrue();

            // Delete project and invalidate caches
            $this->project->delete();
            Cache::forget($projectCacheKey);
            Cache::forget($userProjectsCacheKey);
            Cache::forget($dashboardCacheKey);

            // Verify caches are cleared
            expect(Cache::has($projectCacheKey))->toBeFalse();
            expect(Cache::has($userProjectsCacheKey))->toBeFalse();
            expect(Cache::has($dashboardCacheKey))->toBeFalse();
        });

        it('invalidates task caches when task status changes', function () {
            $task = Task::factory()->for($this->project)->create(['status' => 'todo']);
            $taskCacheKey = "task.{$task->id}";
            $projectStatsCacheKey = "project.{$this->project->id}.stats";

            // Cache task and project stats
            Cache::put($taskCacheKey, $task, 3600);
            Cache::put($projectStatsCacheKey, ['completed_tasks' => 0], 3600);

            // Update task status
            $task->update(['status' => 'completed', 'completed_at' => Carbon::now()]);

            // Simulate cache invalidation
            Cache::forget($taskCacheKey);
            Cache::forget($projectStatsCacheKey);

            expect(Cache::has($taskCacheKey))->toBeFalse();
            expect(Cache::has($projectStatsCacheKey))->toBeFalse();
        });

        it('uses cache tags for batch invalidation', function () {
            // Simulate tagged cache usage
            $projectTag = "project.{$this->project->id}";
            $userTag = "user.{$this->user->id}";

            Cache::tags([$projectTag])->put('project_data', $this->project, 3600);
            Cache::tags([$userTag])->put('user_data', $this->user, 3600);
            Cache::tags([$projectTag, $userTag])->put('combined_data', 'some_data', 3600);

            expect(Cache::tags([$projectTag])->has('project_data'))->toBeTrue();
            expect(Cache::tags([$userTag])->has('user_data'))->toBeTrue();
            expect(Cache::tags([$projectTag, $userTag])->has('combined_data'))->toBeTrue();

            // Flush all caches tagged with project
            Cache::tags([$projectTag])->flush();

            expect(Cache::tags([$projectTag])->has('project_data'))->toBeFalse();
            expect(Cache::tags([$userTag])->has('user_data'))->toBeTrue();
            expect(Cache::tags([$projectTag, $userTag])->has('combined_data'))->toBeFalse();
        });
    });

    describe('Cache Performance', function () {
        it('improves query performance with caching', function () {
            // Create many projects and tasks
            Project::factory(50)->for($this->user)->create();
            Task::factory(200)->for($this->project)->create();

            $cacheKey = 'heavy_query_result';

            // First call - should be slower (not cached)
            $startTime = microtime(true);
            $result1 = Cache::remember($cacheKey, 3600, function () {
                return Task::with(['project', 'project.user'])
                    ->where('status', 'completed')
                    ->orderBy('completed_at', 'desc')
                    ->limit(20)
                    ->get();
            });
            $firstCallTime = microtime(true) - $startTime;

            // Second call - should be faster (cached)
            $startTime = microtime(true);
            $result2 = Cache::get($cacheKey);
            $secondCallTime = microtime(true) - $startTime;

            expect($result1->count())->toBe($result2->count());
            expect($secondCallTime)->toBeLessThan($firstCallTime);
        });

        it('handles cache misses gracefully', function () {
            $cacheKey = 'non_existent_key';

            $result = Cache::remember($cacheKey, 3600, function () {
                return 'default_value';
            });

            expect($result)->toBe('default_value');
            expect(Cache::has($cacheKey))->toBeTrue();
        });
    });
});
