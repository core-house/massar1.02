<?php

declare(strict_types=1);

namespace Modules\Tenancy\Console;

use Illuminate\Console\Command;
use Modules\Tenancy\Models\Tenant;

class CheckSubscriptionExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenancy:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and deactivate tenants at midnight';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Checking subscription expiry...');

        // Find tenants who are active but have no current active subscription
        $tenants = Tenant::where('status', true)->get();
        $deactivatedCount = 0;

        foreach ($tenants as $tenant) {
            // Check if tenant has no active subscription (based on our model's relationship)
            if (!$tenant->activeSubscription()->exists()) {
                $tenant->update(['status' => false]);
                $deactivatedCount++;
                $this->line("Deactivated tenant: {$tenant->id} due to expired subscription.");
            }
        }

        $this->info("Done. Deactivated {$deactivatedCount} tenants.");
    }
}
