<?php

namespace Tubes\Contracts\Windows;

interface WindowableApplication extends CanOwnMenuBars
{
    public function pump(): void;
    public function terminate(): void;
    public function ownsAboutMenu(): bool;
    public function toggleAboutMenuHook(): void;

    public function createWindow(
        string $name,
        int $width,
        int $height,
        ?WindowSurface &$window = null
    ): static;

    public function closeWindow(string $name, ?WindowSurface &$window = null): void;
}
