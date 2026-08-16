<?php

namespace Tubes\Contracts\Windows\Drivers;

use Tubes\Contracts\Windows\WindowableApplication;

interface OSWindowDriver
{
    public function application(): WindowableApplication;
}
