<?php

declare(strict_types=1);

namespace Modules\Agent\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentQuestion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'question_text',
        'answer_text',
        'domain',
        'result_count',
        'processing_time_ms',
        'status',
        'error_message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'result_count' => 'integer',
            'processing_time_ms' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the question.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the query logs for the question.
     */
    public function queryLogs(): HasMany
    {
        return $this->hasMany(AgentQueryLog::class, 'question_id');
    }

    /**
     * Scope a query to only include questions for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include answered questions.
     */
    public function scopeAnswered(Builder $query): Builder
    {
        return $query->where('status', 'answered');
    }

    /**
     * Scope a query to order questions by most recent first.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }
}
