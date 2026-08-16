<?php

namespace Tubes\Windows;

use Fabricate\NutsAndBolts\Collection;
use Tubes\Contracts\Windows\WindowableApplication as WindowableContract;
use Tubes\Contracts\Windows\WindowSurface;

abstract class WindowableApplication implements WindowableContract
{
    protected Collection $windows;
}
