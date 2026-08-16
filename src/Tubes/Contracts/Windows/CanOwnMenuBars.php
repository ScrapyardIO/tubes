<?php

namespace Tubes\Contracts\Windows;

interface CanOwnMenuBars
{
    public function ownsMenuBar(): bool;

    public function menuAddItem(
        string $menuTitle,
        string $itemTitle,
        string $keyEquivalent,
        string $actionId
    ): static;
}
