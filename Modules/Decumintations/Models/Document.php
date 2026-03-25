<?php

declare(strict_types=1);

namespace Modules\Decumintations\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'tags',
        'expiry_date',
        'is_confidential',
        'uploaded_by',
        'related_type',
        'related_id',
    ];

    protected function casts(): array
    {
        return [
            'tags'           => 'array',
            'expiry_date'    => 'date',
            'is_confidential' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Polymorphic: ربط الوثيقة بأي موديل (عميل، موظف، مشروع...)
     */
    public function related(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
