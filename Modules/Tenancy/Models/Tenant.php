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
        'user_position',
        'referral_code',
        'plan_id',
        'subscription_start_at',
        'subscription_end_at',
        'status',
        'enabled_modules',
        'created_by',
    ];

    protected $casts = [
        'subscription_start_at' => 'datetime',
        'subscription_end_at' => 'datetime',
        'enabled_modules' => 'array',
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
            'user_position',
            'referral_code',
            'plan_id',
            'subscription_start_at',
            'subscription_end_at',
            'status',
            'enabled_modules',
            'created_by',
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

    /**
     * Check if a specific module is enabled for this tenant.
     *
     * @param string $module
     * @return bool
     */
    public function hasModule(string $module): bool
    {
        $enabledModules = $this->enabled_modules ?? [];
        return in_array($module, $enabledModules);
    }
}
