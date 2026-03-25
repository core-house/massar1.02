<?php

declare(strict_types=1);

namespace Modules\Agent\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentQueryLog extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question_id',
        'user_id',
        'domain',
        'table_name',
        'operation_type',
        'column_count',
        'filter_count',
        'result_count',
        'execution_time_ms',
        'scopes_applied',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'question_id' => 'integer',
            'user_id' => 'integer',
            'column_count' => 'integer',
            'filter_count' => 'integer',
            'result_count' => 'integer',
            'execution_time_ms' => 'integer',
            'scopes_applied' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the question that owns the query log.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(AgentQuestion::class, 'question_id');
    }

    /**
     * Get the user that owns the query log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
