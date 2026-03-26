<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\ModulesServiceProvider::class, // Must be BEFORE VoltServiceProvider
    App\Providers\VoltServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    Modules\Quality\Providers\QualityServiceProvider::class,
    App\Providers\TenancyServiceProvider::class,
    App\Providers\TenantMigrationServiceProvider::class,
];
