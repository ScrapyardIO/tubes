<?php

namespace Tubes\Windows;

use Tubes\Contracts\Windows\WindowSurface as SurfaceContract;

abstract class WindowSurface implements SurfaceContract
{
    public function __construct(
        public readonly string $window_name,
        public readonly int $pointer
    ) {}

    public function getPointer(): int
    {
        return $this->pointer;
    }
}
