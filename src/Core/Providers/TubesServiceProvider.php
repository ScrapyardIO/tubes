<?php

namespace ScrapyardIO\Tubes\Core\Providers;

use Fabricate\Core\Machine as ScrapyardIOMachine;
use Fabricate\NutsAndBolts\AggregateServiceProvider;
use Fabricate\Contracts\Chassis\BindingResolutionException;
use ScrapyardIO\Tubes\Color\ColorPanelServiceProvider;
use ScrapyardIO\Tubes\Matrix\RGBMatrixServiceProvider;
use ScrapyardIO\Tubes\Monochrome\MonochromePanelServiceProvider;

class TubesServiceProvider extends AggregateServiceProvider
{
    protected array $providers = [
        ColorPanelServiceProvider::class,
        RGBMatrixServiceProvider::class,
        MonochromePanelServiceProvider::class,
    ];

    /**
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $this->publishConfig();

        parent::register();
    }

    /**
     * @throws BindingResolutionException
     */
    protected function publishConfig(): void
    {
        $source = realpath($raw = __DIR__.'/../../../config/tubes.php') ?: $raw;

        if ($this->program instanceof ScrapyardIOMachine && $this->program->runningInConsole()) {
            $this->publishes([$source => $this->program->configPath('tubes.php')]);
        }

        $this->mergeConfigFrom($source, 'tubes');
    }
}