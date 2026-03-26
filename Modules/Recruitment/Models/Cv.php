<?php

declare(strict_types=1);

namespace Modules\Recruitment\Models;

use Spatie\MediaLibrary\HasMedia;
use Modules\Branches\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\Scopes\BranchScope;

class Cv extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = ['id'];

    protected $table = 'cvs';

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
        $this->addMediaCollection('HR_Cvs')
            ->useDisk('public')
            ->singleFile();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function jobPosting(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function interviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function contract(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Contract::class);
    }
}

