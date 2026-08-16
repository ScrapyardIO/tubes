<?php

namespace Tubes\Windows\Drivers;

use Tubes\Windows\WindowableApplication;
use Tubes\Contracts\Windows\Drivers\OSWindowDriver as DriverContract;

abstract class OSWindowDriver implements DriverContract
{
    protected WindowableApplication $os_app;

    abstract public function application(): WindowableApplication;
}
