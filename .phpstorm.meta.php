<?php

namespace PHPSTORM_META {

    // Stancl/Tenancy Global Helpers
    // Will be used when stancl/tenancy is installed
    override(\tenant(), map([
        '' => '@',
    ]));

    registerArgumentsSet('tenant_keys',
        'id',
        'domain',
        'database',
        'tenancy_db_name'
    );

    expectedArguments(\tenant(), 0, argumentsSet('tenant_keys'));
}
