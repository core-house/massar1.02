<?php

declare(strict_types=1);

namespace Modules\CRM\Observers;

use Modules\CRM\Models\Task;
use Modules\CRM\Models\TaskActivityLog;

class TaskObserver
{
    /**
     * Fields to track for changes (excluding timestamps and audit fields).
     */
    private const TRACKED_FIELDS = [
        'title',
        'description',
        'status',
        'priority',
        'user_id',
        'client_id',
        'task_type_id',
        'start_date',
        'due_date',
        'duration',
        'client_comment',
        'user_comment',
    ];

    public function created(Task $task): void
    {
        TaskActivityLog::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'event'   => 'created',
        ]);
    }

    public function updating(Task $task): void
    {
        $dirty = $task->getDirty();
        $userId = auth()->id();

        foreach ($dirty as $field => $newValue) {
            if (!in_array($field, self::TRACKED_FIELDS, true)) {
                continue;
            }

            $oldValue = $task->getOriginal($field);

            // Cast enum values to their string representation for readability
            $oldValue = $this->castValue($task, $field, $oldValue);
            $newValue = $this->castValue($task, $field, $newValue);

            $event = $field === 'status' ? 'status_changed' : 'updated';

            TaskActivityLog::create([
                'task_id'   => $task->id,
                'user_id'   => $userId,
                'event'     => $event,
                'field'     => $field,
                'old_value' => (string) ($oldValue ?? ''),
                'new_value' => (string) ($newValue ?? ''),
            ]);
        }
    }

    /**
     * Cast enum/date values to human-readable strings.
     */
    private function castValue(Task $task, string $field, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        // Already an enum instance (e.g. from getDirty() on cast fields)
        if ($value instanceof \BackedEnum) {
            return method_exists($value, 'label') ? $value->label() : $value->value;
        }

        $casts = $task->getCasts();

        if (!isset($casts[$field])) {
            return $value;
        }

        $castType = $casts[$field];

        if (enum_exists($castType)) {
            $enum = $castType::tryFrom((string) $value);
            return $enum ? (method_exists($enum, 'label') ? $enum->label() : $enum->value) : $value;
        }

        return $value;
    }
}
