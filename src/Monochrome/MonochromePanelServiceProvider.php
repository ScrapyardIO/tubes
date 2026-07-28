<?php

namespace ScrapyardIO\Tubes\Monochrome;

use Fabricate\Contracts\Chassis\CircularDependencyException;
use Fabricate\NutsAndBolts\MagicAliases\Display;
use Fabricate\NutsAndBolts\ServiceProvider;

class MonochromePanelServiceProvider extends ServiceProvider
{
    public function register(): void {}

    /**
     * @throws CircularDependencyException
     */
    protected function enabled(): bool
    {
        return config('tubes.monochrome.enabled', false);
    }

    /**
     * @throws CircularDependencyException
     */
    public function boot(): void {
        if($this->enabled()) {
            Display::addEPanel('monochrome', MonochromePanel::class);
        }
    }
}