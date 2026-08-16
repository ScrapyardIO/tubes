<?php

namespace Tubes\Windows\Drivers;

use Microscrap\ScrapyardIO\AppKit\AppKitApplication;
use Tubes\Contracts\Windows\Drivers\MacOSWindowDriver as DriverContract;

class AppKitWindowDriver extends OSWindowDriver implements DriverContract
{
    public function __construct(
        public readonly array $config
    )
    {
        $this->os_app = new AppKitApplication($config['app_name']);
    }

    public function application(): AppKitApplication
    {
        return $this->os_app;
    }
}
