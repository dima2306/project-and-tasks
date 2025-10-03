<?php

use App\Enums\TaskStatus;

describe('TaskStatus Enum', function () {
    describe('Enum Values', function () {
        it('has correct enum cases', function () {
            $cases = TaskStatus::cases();

            expect($cases)->toHaveCount(3);
            expect($cases[0]->value)->toBe('todo');
            expect($cases[1]->value)->toBe('in_progress');
            expect($cases[2]->value)->toBe('completed');
        });

        it('returns correct values array', function () {
            $values = TaskStatus::values();

            expect($values)->toBe(['todo', 'in_progress', 'completed']);
        });

        it('provides validation rule string', function () {
            $rule = TaskStatus::validationRule();

            expect($rule)->toBe('in:todo,in_progress,completed');
        });

        it('returns active statuses', function () {
            $activeStatuses = TaskStatus::activeStatuses();

            expect($activeStatuses)->toBe(['todo', 'in_progress']);
        });
    });

    describe('Status Methods', function () {
        it('correctly identifies completed status', function () {
            expect(TaskStatus::TODO->isCompleted())->toBeFalse();
            expect(TaskStatus::IN_PROGRESS->isCompleted())->toBeFalse();
            expect(TaskStatus::COMPLETED->isCompleted())->toBeTrue();
        });

        it('correctly identifies active status', function () {
            expect(TaskStatus::TODO->isActive())->toBeTrue();
            expect(TaskStatus::IN_PROGRESS->isActive())->toBeTrue();
            expect(TaskStatus::COMPLETED->isActive())->toBeFalse();
        });

        it('returns correct next status', function () {
            expect(TaskStatus::TODO->getNextStatus())->toBe(TaskStatus::IN_PROGRESS);
            expect(TaskStatus::IN_PROGRESS->getNextStatus())->toBe(TaskStatus::COMPLETED);
            expect(TaskStatus::COMPLETED->getNextStatus())->toBeNull();
        });

        it('provides human-readable labels', function () {
            expect(TaskStatus::TODO->label())->toBe('To Do');
            expect(TaskStatus::IN_PROGRESS->label())->toBe('In Progress');
            expect(TaskStatus::COMPLETED->label())->toBe('Completed');
        });

        it('provides CSS classes', function () {
            expect(TaskStatus::TODO->cssClass())->toBe('status-todo');
            expect(TaskStatus::IN_PROGRESS->cssClass())->toBe('status-progress');
            expect(TaskStatus::COMPLETED->cssClass())->toBe('status-completed');
        });
    });

    describe('Enum Creation', function () {
        it('can create enum from string value', function () {
            $todo = TaskStatus::from('todo');
            $inProgress = TaskStatus::from('in_progress');
            $completed = TaskStatus::from('completed');

            expect($todo)->toBe(TaskStatus::TODO);
            expect($inProgress)->toBe(TaskStatus::IN_PROGRESS);
            expect($completed)->toBe(TaskStatus::COMPLETED);
        });

        it('throws exception for invalid enum value', function () {
            expect(fn () => TaskStatus::from('invalid'))->toThrow(ValueError::class);
        });

        it('returns null for invalid value with tryFrom', function () {
            expect(TaskStatus::tryFrom('invalid'))->toBeNull();
            expect(TaskStatus::tryFrom('todo'))->toBe(TaskStatus::TODO);
        });
    });
});
