<?php

declare(strict_types=1);

namespace Modules\Recruitment\Models;

use Spatie\MediaLibrary\HasMedia;
use Modules\Branches\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\BranchScope;
use App\Models\User;
use App\Models\Employee;

class Onboarding extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'completion_date' => 'date',
        'checklist' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('onboarding_documents')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    }

    /**
     * Get all document URLs for this onboarding
     * Works correctly in both local (Laragon) and production environments
     * Uses asset() helper to ensure correct URL generation
     */
    public function getDocumentUrlsAttribute(): array
    {
        $mediaItems = $this->getMedia('onboarding_documents');
        
        return $mediaItems->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'url' => asset('storage/'.$media->id.'/'.$media->file_name),
                'created_at' => $media->created_at,
            ];
        })->toArray();
    }

    /**
     * Get a specific document URL by media ID
     */
    public function getDocumentUrl(int $mediaId): ?string
    {
        $media = $this->getMedia('onboarding_documents')->where('id', $mediaId)->first();
        
        if (!$media) {
            return null;
        }
        
        return asset('storage/'.$media->id.'/'.$media->file_name);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}

