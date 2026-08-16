<?php

namespace Tubes\Windows;

use Fabricate\NutsAndBolts\Manager;
use Tubes\Windows\Drivers\GTKWindowDriver;
use Tubes\Windows\Drivers\AppKitWindowDriver;
use Tubes\Contracts\Windows\WindowableApplication;

class WindowManager extends Manager
{
    public function createMacDriver(): AppKitWindowDriver
    {
        return new AppKitWindowDriver(
            config('windows.mac', [])
        );
    }

    public function createLinuxDriver(): GTKWindowDriver
    {
        return new GTKWindowDriver(
            config('windows.linux', [])
        );
    }

    public function app(?string $driver = null): WindowableApplication
    {
        $driver = $this->driver($driver);
        return $driver->application();
    }

    public function getDefaultDriver(): ?string
    {
        return str_contains(php_uname(), 'Darwin') ? 'mac' : 'linux';
    }
}
