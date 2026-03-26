<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use App\Models\{User, Client};
use Modules\Branches\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ReturnOrder extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'crm_returns';

    protected $fillable = [
        'return_number',
        'client_id',
        'created_by',
        'approved_by',
        'original_invoice_number',
        'original_invoice_date',
        'return_date',
        'status',
        'return_type',
        'reason',
        'notes',
        'attachment',
        'total_amount',
        'refund_amount',
        'branch_id',
    ];

    protected $casts = [
        'return_date' => 'date',
        'original_invoice_date' => 'date',
        'total_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\BranchScope);

        static::creating(function ($return) {
            if (empty($return->return_number)) {
                $todayPrefix = 'RET-' . date('Ymd') . '-';
                $lastReturn = static::withoutGlobalScopes()
                    ->where('return_number', 'like', $todayPrefix . '%')
                    ->latest('id')
                    ->first();

                if ($lastReturn) {
                    $lastNumber = intval(substr($lastReturn->return_number, -4));
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $return->return_number = $todayPrefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);

                while (static::withoutGlobalScopes()->where('return_number', $return->return_number)->exists()) {
                    $newNumber++;
                    $return->return_number = $todayPrefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
                }
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function updateStatus($newStatus)
    {
        $this->update(['status' => $newStatus]);
    }

    public function calculateTotal()
    {
        $this->total_amount = $this->items()->sum('total_price');
        $this->save();
    }

    /**
     * Register media collections for return attachments
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('return-attachments')
            ->useDisk('public')
            ->acceptsMimeTypes([
                'application/pdf',
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/gif',
                'image/webp'
            ]);

        $this->addMediaCollection('return-images')
            ->useDisk('public')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/gif',
                'image/webp'
            ]);
    }

    /**
     * Register media conversions for image optimization
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('preview')
            ->width(400)
            ->height(400)
            ->sharpen(5)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(800)
            ->height(800)
            ->sharpen(3)
            ->nonQueued();
    }
}
