<?php

namespace Tubes\Windows\Drivers;

use Microscrap\ScrapyardIO\GTK\GTKApplication;
use Tubes\Contracts\Windows\Drivers\LinuxWindowDriver as DriverContract;

class GTKWindowDriver extends OSWindowDriver implements DriverContract
{
    public function __construct(
        public readonly array $config
    ) {
        $this->os_app = new GTKApplication(
            $config['application_id'],
            $config['application_flags']
        );
    }

    public function application(): GTKApplication
    {
        return $this->os_app;
    }
}
