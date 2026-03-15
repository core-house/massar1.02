<?php

namespace Modules\CRM\Models;

use App\Models\User;
use App\Models\Client;
use Spatie\MediaLibrary\HasMedia;
use Modules\Branches\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Modules\CRM\Enums\{TaskStatusEnum, TaskPriorityEnum};

class Task extends Model  implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'priority' => TaskPriorityEnum::class,
        'status' => TaskStatusEnum::class,
        'due_date' => 'date',
        'duration' => 'float',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\BranchScope);

        // Auto-set created_by on creation
        static::creating(function ($task) {
            if (auth()->check() && !$task->created_by) {
                $task->created_by = auth()->id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function taskType()
    {
        return $this->belongsTo(\Modules\CRM\Models\TaskType::class, 'task_type_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200);
    }

    // تحديد مسار حفظ الملفات
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('tasks')
            ->useDisk('public')
            ->useFallbackUrl('/images/placeholder.jpg')
            ->useFallbackPath(public_path('/images/placeholder.jpg'));
    }

    /**
     * Get formatted duration (removes unnecessary decimals)
     */
    public function getFormattedDurationAttribute(): ?string
    {
        if (!$this->duration) {
            return null;
        }

        // Remove trailing zeros and decimal point if not needed
        return rtrim(rtrim(number_format($this->duration, 2, '.', ''), '0'), '.');
    }
}
