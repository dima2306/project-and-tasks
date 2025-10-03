<?php

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->project = Project::factory()->for($this->user)->create();
});

describe('TaskStatus Integration', function () {
    describe('Model Integration', function () {
        it('casts status to enum in model', function () {
            $task = Task::factory()->for($this->project)->create(['status' => 'todo']);

            expect($task->status)->toBeInstanceOf(TaskStatus::class);
            expect($task->status)->toBe(TaskStatus::TODO);
        });

        it('uses enum helper methods on model', function () {
            $todoTask = Task::factory()->for($this->project)->todo()->create();
            $completedTask = Task::factory()->for($this->project)->completed()->create();
            expect($todoTask->isCompleted())->toBeFalse();
            expect($completedTask->isCompleted())->toBeTrue();
        });

        it('updates status using model helper methods', function () {
            $task = Task::factory()->for($this->project)->todo()->create();

            $task->markInProgress();
            expect($task->fresh()->status)->toBe(TaskStatus::IN_PROGRESS);
            expect($task->fresh()->completed_at)->toBeNull();

            $task->markCompleted();
            expect($task->fresh()->status)->toBe(TaskStatus::COMPLETED);
            expect($task->fresh()->completed_at)->not->toBeNull();

            $task->markTodo();
            expect($task->fresh()->status)->toBe(TaskStatus::TODO);
            expect($task->fresh()->completed_at)->toBeNull();
        });
    });

    describe('Factory Integration', function () {
        it('creates tasks with specific status using factory states', function () {
            $todoTask = Task::factory()->for($this->project)->todo()->create();
            $progressTask = Task::factory()->for($this->project)->inProgress()->create();
            $completedTask = Task::factory()->for($this->project)->completed()->create();

            expect($todoTask->status)->toBe(TaskStatus::TODO);
            expect($progressTask->status)->toBe(TaskStatus::IN_PROGRESS);
            expect($completedTask->status)->toBe(TaskStatus::COMPLETED);

            expect($completedTask->completed_at)->not->toBeNull();
            expect($todoTask->completed_at)->toBeNull();
        });

        it('creates random valid statuses in default factory', function () {
            $tasks = Task::factory(50)->for($this->project)->create();

            $statuses = $tasks->pluck('status')->unique();
            $statuses->each(function ($status) {
                expect($status)->toBeInstanceOf(TaskStatus::class);
                expect(in_array($status->value, TaskStatus::values()))->toBeTrue();
            });
        });
    });

    describe('Validation Integration', function () {
        it('validates status using enum validation rule', function () {
            $validationRules = [
                'status' => ['required', TaskStatus::validationRule()],
            ];

            $validator = validator(['status' => 'todo'], $validationRules);
            expect($validator->passes())->toBeTrue();

            $validator = validator(['status' => 'invalid'], $validationRules);
            expect($validator->passes())->toBeFalse();
            expect($validator->errors()->first('status'))->toContain('invalid');
        });

        it('can use enum values in request validation', function () {
            // Simulate request data validation
            $validData = ['status' => TaskStatus::IN_PROGRESS->value];
            $invalidData = ['status' => 'invalid_status'];

            $rules = ['status' => 'required|' . TaskStatus::validationRule()];

            expect(validator($validData, $rules)->passes())->toBeTrue();
            expect(validator($invalidData, $rules)->passes())->toBeFalse();
        });
    });

    describe('Query Integration', function () {
        beforeEach(function () {
            Task::factory()->for($this->project)->todo()->count(3)->create();
            Task::factory()->for($this->project)->inProgress()->count(2)->create();
            Task::factory()->for($this->project)->completed()->count(4)->create();
        });

        it('queries tasks by enum status', function () {
            $todoTasks = Task::where('status', TaskStatus::TODO)->get();
            $completedTasks = Task::where('status', TaskStatus::COMPLETED)->get();

            expect($todoTasks)->toHaveCount(3);
            expect($completedTasks)->toHaveCount(4);
        });

        it('queries active tasks using enum helper', function () {
            $activeTasks = Task::whereIn('status', TaskStatus::activeStatuses())->get();

            expect($activeTasks)->toHaveCount(5); // 3 todo + 2 in_progress
        });

        it('filters using enum methods in collections', function () {
            $allTasks = Task::all();

            $activeTasks = $allTasks->filter(fn($task) => $task->status->isActive());
            $completedTasks = $allTasks->filter(fn($task) => $task->status->isCompleted());

            expect($activeTasks)->toHaveCount(5);
            expect($completedTasks)->toHaveCount(4);
        });
    });
});
