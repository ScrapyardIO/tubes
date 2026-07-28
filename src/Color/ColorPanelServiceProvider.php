<?php

namespace ScrapyardIO\Tubes\Color;

use Fabricate\Contracts\Chassis\CircularDependencyException;
use Fabricate\NutsAndBolts\MagicAliases\Display;
use Fabricate\NutsAndBolts\ServiceProvider;

class ColorPanelServiceProvider extends ServiceProvider
{
    public function register(): void {}

    /**
     * @throws CircularDependencyException
     */
    protected function enabled(): bool
    {
        return config('tubes.color.enabled', false);
    }

    /**
     * @throws CircularDependencyException
     */
    public function boot(): void {
        if($this->enabled()) {
            Display::addEPanel('color', ColorPanel::class);
        }
    }
}