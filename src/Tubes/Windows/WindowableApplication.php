<?php

namespace Tubes\Windows;

use Fabricate\NutsAndBolts\Collection;
use Tubes\Contracts\Windows\WindowableApplication as WindowableContract;
use Tubes\Contracts\Windows\WindowSurface;

abstract class WindowableApplication implements WindowableContract
{
    protected Collection $windows;

    public function closeWindow(string $name, ?WindowSurface &$window = null): void
    {
        $existing = $this->windows->has($name)
            ? $this->windows->offsetGet($name)
            : $window;

        if ($existing instanceof WindowSurface) {
            $existing->close();
        }

        $this->windows->offsetUnset($name);
        $window = null;
    }
}
