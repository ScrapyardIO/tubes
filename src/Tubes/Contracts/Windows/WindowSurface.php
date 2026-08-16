<?php

namespace Tubes\Contracts\Windows;

interface WindowSurface extends CanOwnMenuBars
{
    public function close(): void;
}
