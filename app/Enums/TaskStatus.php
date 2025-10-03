<?php

namespace App\Enums;

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }

    public static function activeStatuses(): array
    {
        return [self::TODO->value, self::IN_PROGRESS->value];
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isActive(): bool
    {
        return in_array($this->value, self::activeStatuses());
    }

    /**
     * Get the next logical status in workflow.
     */
    public function getNextStatus(): ?self
    {
        return match($this) {
            self::TODO => self::IN_PROGRESS,
            self::IN_PROGRESS => self::COMPLETED,
            self::COMPLETED => null,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::TODO => 'To Do',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
        };
    }

    /** Get CSS class for UI styling.*/
    public function cssClass(): string
    {
        return match($this) {
            self::TODO => 'status-todo',
            self::IN_PROGRESS => 'status-progress',
            self::COMPLETED => 'status-completed',
        };
    }
}
