<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use App\Models\User;
use App\Models\Client;
use Modules\Branches\Models\Branch;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'crm_tickets';

    protected $fillable = [
        'ticket_number',
        'client_id',
        'assigned_to',
        'created_by',
        'subject',
        'description',
        'ticket_type',
        'ticket_reference',
        'opened_date',
        'response_deadline',
        'status',
        'status_title',
        'priority',
        'branch_id',
    ];

    protected $casts = [
        'opened_date' => 'date',
        'response_deadline' => 'date',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Models\Scopes\BranchScope);

        static::creating(function (self $ticket): void {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
        });
    }

    /**
     * توليد رقم تذكرة فريد بصيغة TKT-YYYYMMDD-XXXX
     */
    private static function generateTicketNumber(): string
    {
        $prefix = 'TKT-' . date('Ymd') . '-';

        $last = static::withoutGlobalScopes()
            ->where('ticket_number', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $next = $last
            ? (int) substr($last->ticket_number, -4) + 1
            : 1;

        $number = $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);

        // ضمان عدم التكرار في حالة التزامن
        while (static::withoutGlobalScopes()->where('ticket_number', $number)->exists()) {
            $next++;
            $number = $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
        }

        return $number;
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class, 'ticket_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function updateStatus($newStatus)
    {
        $this->update(['status' => $newStatus]);
    }
}
