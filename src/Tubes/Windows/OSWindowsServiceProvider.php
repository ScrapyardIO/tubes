<?php

namespace Tubes\Windows;

use Fabricate\NutsAndBolts\ServiceProvider;

class OSWindowsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/windows.php',
            'windows',
        );

        $this->container->singleton(WindowManager::class, fn ($app) => new WindowManager($app));
        $this->container->singleton('window', fn ($app) => $app->make(WindowManager::class));
    }

    public function boot(): void
    {
        $source = realpath($raw = __DIR__.'/../../../config/windows.php') ?: $raw;
        $inConsole = PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg';

        if ($inConsole && method_exists($this->container, 'configPath')) {
            $this->publishes(
                [$source => $this->container->configPath('windows.php')],
                'tubes-windows-config',
            );
        }
    }
}
