<?php

declare(strict_types=1);

namespace Modules\Tenancy\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'name',
        'domain',
        'contact_number',
        'address',
        'company_name',
        'company_size',
        'admin_email',
        'admin_password',
        'user_position',
        'referral_code',
        'plan_id',
        'subscription_start_at',
        'subscription_end_at',
        'status',
        'data'
    ];

        protected $casts = [
        'subscription_start_at' => 'datetime',
        'subscription_end_at' => 'datetime',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'domain',
            'contact_number',
            'address',
            'company_name',
            'company_size',
            'admin_email',
            'admin_password',
            'user_position',
            'referral_code',
            'plan_id',
            'subscription_start_at',
            'subscription_end_at',
            'status',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
