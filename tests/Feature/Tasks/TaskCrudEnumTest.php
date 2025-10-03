<?php

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();

    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);

    $this->admin->assignRole('admin');
    $this->user->assignRole('user');
    $this->otherUser->assignRole('user');

    $this->userProject = Project::factory()->for($this->user)->create();
    $this->otherProject = Project::factory()->for($this->otherUser)->create();
});

describe('Task CRUD Operations with Enum', function () {
    describe('Create Task', function () {
        it('allows project owner to create task with enum status', function () {
            $taskData = [
                'project_id' => $this->userProject->id,
                'title' => 'Test Task',
                'description' => 'Test description',
                'status' => TaskStatus::TODO->value,
            ];

            $task = Task::create($taskData);

            $this->assertDatabaseHas('tasks', [
                'project_id' => $this->userProject->id,
                'title' => 'Test Task',
                'status' => TaskStatus::TODO->value,
            ]);

            expect($task->status)->toBe(TaskStatus::TODO);
        });

        it('validates enum status values in creation', function () {
            // Test validation logic directly
            $validator = validator([
                'project_id' => $this->userProject->id,
                'title' => 'Test Task',
                'description' => 'A test description',
                'status' => 'invalid_status',
            ], [
                'project_id' => 'required|exists:projects,id',
                'title' => 'required|string|max:100',
                'description' => 'required|string',
                'status' => TaskStatus::validationRule(),
            ]);

            expect($validator->passes())->toBeFalse();
            expect($validator->errors()->has('status'))->toBeTrue();
        });

        it('accepts all valid enum values in creation', function () {
            foreach (TaskStatus::cases() as $status) {
                $taskData = [
                    'project_id' => $this->userProject->id,
                    'title' => "Test {$status->value}",
                    'description' => 'A test description',
                    'status' => $status->value,
                ];

                $task = Task::create($taskData);

                // Look for the correct title format
                $createdTask = Task::where('title', "Test {$status->value}")->first();
                expect($createdTask)->not->toBeNull();
                expect($createdTask->status)->toBe($status);
            }
        });
    });

    describe('Update Task with Enum', function () {
        it('updates task status using enum helper methods', function () {
            $task = Task::factory()->for($this->userProject)->todo()->create();

            // Test model helper methods
            $task->markInProgress();
            expect($task->fresh()->status)->toBe(TaskStatus::IN_PROGRESS);

            $task->markCompleted();
            $freshTask = $task->fresh();
            expect($freshTask->status)->toBe(TaskStatus::COMPLETED);
            expect($freshTask->completed_at)->not->toBeNull();

            $task->markTodo();
            $freshTask = $task->fresh();
            expect($freshTask->status)->toBe(TaskStatus::TODO);
            expect($freshTask->completed_at)->toBeNull();
        });

        it('handles enum transitions through direct model updates', function () {
            $task = Task::factory()->for($this->userProject)->todo()->create();

            // Test full workflow through direct model updates
            $task->update(['status' => TaskStatus::IN_PROGRESS]);
            expect($task->fresh()->status)->toBe(TaskStatus::IN_PROGRESS);

            $task->update([
                'status' => TaskStatus::COMPLETED,
                'completed_at' => now(),
            ]);

            $freshTask = $task->fresh();
            expect($freshTask->status)->toBe(TaskStatus::COMPLETED);
            expect($freshTask->completed_at)->not->toBeNull();
        });
    });

    describe('Task Status Management with Enum', function () {
        it('uses factory states for different statuses', function () {
            $todoTask = Task::factory()->for($this->userProject)->todo()->create();
            $progressTask = Task::factory()->for($this->userProject)->inProgress()->create();
            $completedTask = Task::factory()->for($this->userProject)->completed()->create();

            expect($todoTask->status)->toBe(TaskStatus::TODO);
            expect($progressTask->status)->toBe(TaskStatus::IN_PROGRESS);
            expect($completedTask->status)->toBe(TaskStatus::COMPLETED);
            expect($completedTask->isCompleted())->toBeTrue();
        });

        it('filters tasks by enum status using queries', function () {
            Task::factory()->for($this->userProject)->todo()->count(3)->create();
            Task::factory()->for($this->userProject)->inProgress()->count(2)->create();
            Task::factory()->for($this->userProject)->completed()->count(4)->create();

            // Test query counts directly
            expect(Task::where('status', TaskStatus::TODO)->count())->toBe(3);
            expect(Task::where('status', TaskStatus::IN_PROGRESS)->count())->toBe(2);
            expect(Task::where('status', TaskStatus::COMPLETED)->count())->toBe(4);

            // Test using active statuses helper
            $activeTasks = Task::whereIn('status', TaskStatus::activeStatuses())->get();
            expect($activeTasks)->toHaveCount(5); // 3 todo + 2 in_progress
        });

        it('can transition between all enum states', function () {
            $task = Task::factory()->for($this->userProject)->todo()->create();

            // todo -> in_progress
            $task->update(['status' => TaskStatus::IN_PROGRESS]);
            expect($task->fresh()->status)->toBe(TaskStatus::IN_PROGRESS);

            // in_progress -> completed
            $task->update([
                'status' => TaskStatus::COMPLETED,
                'completed_at' => now(),
            ]);
            expect($task->fresh()->status)->toBe(TaskStatus::COMPLETED);
            expect($task->fresh()->completed_at)->not->toBeNull();

            // completed -> todo (reverting)
            $task->update([
                'status' => TaskStatus::TODO,
                'completed_at' => null,
            ]);
            expect($task->fresh()->status)->toBe(TaskStatus::TODO);
            expect($task->fresh()->completed_at)->toBeNull();
        });
    });

    describe('Enum Validation and Business Logic', function () {
        it('validates enum values in model creation', function () {
            // Valid enum values should work
            foreach (TaskStatus::cases() as $status) {
                $task = Task::factory()->for($this->userProject)->create([
                    'status' => $status->value,
                ]);
                expect($task->status)->toBe($status);
            }
        });

        it('provides enum helper methods on model', function () {
            $todoTask = Task::factory()->for($this->userProject)->todo()->create();
            $completedTask = Task::factory()->for($this->userProject)->completed()->create();

            // Test isCompleted method
            expect($todoTask->isCompleted())->toBeFalse();
            expect($completedTask->isCompleted())->toBeTrue();
        });

        it('handles enum workflow correctly', function () {
            $task = Task::factory()->for($this->userProject)->todo()->create();

            // Use enum getNextStatus method
            $nextStatus = $task->status->getNextStatus();
            expect($nextStatus)->toBe(TaskStatus::IN_PROGRESS);

            $task->update(['status' => $nextStatus]);

            $nextStatus = $task->status->getNextStatus();
            expect($nextStatus)->toBe(TaskStatus::COMPLETED);

            $task->update(['status' => $nextStatus, 'completed_at' => now()]);

            $nextStatus = $task->status->getNextStatus();
            expect($nextStatus)->toBeNull(); // No next status after completed
        });
    });
});
