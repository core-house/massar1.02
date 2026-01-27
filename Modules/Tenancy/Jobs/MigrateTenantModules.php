<?php

namespace Modules\Tenancy\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Modules\Tenancy\Models\Tenant;

class MigrateTenantModules implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle()
    {
        // التأكد إننا في tenant database context
        tenancy()->initialize($this->tenant);

        // شغّل كل module migrations
        Artisan::call('module:migrate', [
            '--all' => true,
            '--force' => true,
            '--database' => 'tenant',
            '--subpath' => 'tenant',
        ]);
    }
}
