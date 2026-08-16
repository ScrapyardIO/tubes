<?php

namespace Tubes\Core\Providers;

use Fabricate\NutsAndBolts\AggregateServiceProvider;
use Tubes\Windows\OSWindowsServiceProvider;

class TubesServiceProvider extends AggregateServiceProvider
{
    protected array $providers = [
        OSWindowsServiceProvider::class,
    ];
}
