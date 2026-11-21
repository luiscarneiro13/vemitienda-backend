<?php

use App\Core\Providers\CoreServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;

return [
    AppServiceProvider::class,
    CoreServiceProvider::class,
    AdminPanelProvider::class,
];
