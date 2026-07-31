<?php

namespace ScrapyardIO\Tubes\Matrix;

use Fabricate\Contracts\Chassis\CircularDependencyException;
use Fabricate\NutsAndBolts\MagicAliases\Display;
use Fabricate\NutsAndBolts\ServiceProvider;

class RGBMatrixServiceProvider extends ServiceProvider
{
    public function register(): void {}

    /**
     * @throws CircularDependencyException
     */
    public function boot(): void
    {
        if (config('tubes.rgb-matrix.enabled', false)) {
            Display::addEPanel('rgb-matrix', RGBMatrix::class);
        }
    }
}
