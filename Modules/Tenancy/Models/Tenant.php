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
        'status',
        'enabled_modules',
        'created_by',
    ];

    protected $casts = [
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

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->latest();
    }

    public function isActive(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function getSubscriptionEndDate(): ?\Carbon\Carbon
    {
        $latest = $this->subscriptions()->latest()->first();
        return $latest ? $latest->ends_at : null;
    }

    public function getSubscriptionStartDate(): ?\Carbon\Carbon
    {
        $latest = $this->subscriptions()->latest()->first();
        return $latest ? $latest->starts_at : null;
    }
}
