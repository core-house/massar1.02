<?php

namespace Modules\Tenancy\Console;

use Illuminate\Console\Command;
use Stancl\Tenancy\Database\Models\Tenant;

class TenantsMigrateFreshSeed extends Command
{
    protected $signature = 'tenants:migrate-fresh-seed {--tenants=* : Specific tenants (default: all)}';

    protected $description = 'Migrate fresh + seed all tenants';

    public function handle()
    {
        $tenants = $this->option('tenants')
            ? Tenant::whereIn('id', $this->option('tenants'))->get()
            : Tenant::all();

        $this->info("🎯 Processing {$tenants->count()} tenants...\n");

        foreach ($tenants as $tenant) {
            $this->info("🔄 Tenant: {$tenant->id}");

            // Switch to tenant
            tenancy()->initialize($tenant);

            // Migrate fresh
            $this->call('migrate:fresh');

            // Seed
            $this->call('db:seed');

            $this->line("✅ {$tenant->id} DONE!\n");
        }

        $this->info('🎉 ALL TENANTS migrate:fresh --seed COMPLETED!');
    }
}
