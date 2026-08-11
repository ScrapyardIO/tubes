<?php

namespace ScrapyardIO\Tubes\Core\Providers;

use Fabricate\Contracts\Sketches\SketchRegistry;
use Fabricate\Core\Console\AboutCommand;
use Fabricate\NutsAndBolts\AggregateServiceProvider;
use ScrapyardIO\Tubes\Core\Console\InstallGfxCommand;
use ScrapyardIO\Tubes\Core\Console\UninstallGfxCommand;
use ScrapyardIO\Tubes\Core\Runner\Sketches\CanvasWindowDemo;
use ScrapyardIO\Tubes\Fonts\Console\FontMakeCommand;
use ScrapyardIO\Tubes\Fonts\Providers\FontsServiceProvider;
use ScrapyardIO\Tubes\Framebuffers\Providers\FramebuffersServiceProvider;
use ScrapyardIO\Tubes\Panels\Providers\PanelsServiceProvider;
use ScrapyardIO\Tubes\Windows\Providers\WindowsServiceProvider;

class TubesServiceProvider extends AggregateServiceProvider
{
    protected array $providers = [
        FramebuffersServiceProvider::class,
        WindowsServiceProvider::class,
        PanelsServiceProvider::class,
        FontsServiceProvider::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__, 4).'/config/tubes.php',
            'tubes',
        );

        parent::register();
    }

    public function boot(): void
    {
        $this->syncSubsystemDefaults();
        $this->registerAboutDrivers();
        $this->registerSketches();

        if ($this->container->runningInConsole()) {
            $this->publishes([
                dirname(__DIR__, 4).'/config/tubes.php' => $this->container->configPath('tubes.php'),
            ], 'tubes-config');

            // ContainerCommandLoader::has() requires the class to be bound on the container.
            $this->container->singleton(InstallGfxCommand::class);
            $this->container->singleton(UninstallGfxCommand::class);
            $this->container->singleton(FontMakeCommand::class);

            $this->commands([
                InstallGfxCommand::class,
                UninstallGfxCommand::class,
                FontMakeCommand::class,
            ]);
        }
    }

    /**
     * Contribute Canvas / Framebuffer / Font defaults to Workshop `about` Drivers.
     */
    protected function registerAboutDrivers(): void
    {
        if (! class_exists(AboutCommand::class)) {
            return;
        }

        AboutCommand::add('Drivers', function (): array {
            return array_filter([
                'Canvas' => config('tubes.defaults.canvas'),
                'Framebuffers' => config('tubes.defaults.framebuffer') ?: config('framebuffers.default'),
                'Fonts' => config('tubes.defaults.font') ?: config('fonts.default'),
            ], static fn (mixed $value): bool => is_string($value) && $value !== '');
        });
    }

    /**
     * Keep framebuffers/fonts.default aligned with tubes.defaults.*.
     *
     * Window driver fallback stays on config('windows.default') — presentation
     * defaults use tubes.defaults.canvas (a windows.* or panels.* profile slug).
     */
    protected function syncSubsystemDefaults(): void
    {
        if (! $this->container->bound('config')) {
            return;
        }

        $config = $this->container->make('config');

        $framebuffer = $config->get('tubes.defaults.framebuffer');
        if (is_string($framebuffer) && $framebuffer !== '') {
            $config->set('framebuffers.default', $framebuffer);
        }

        $font = $config->get('tubes.defaults.font');
        if (is_string($font) && $font !== '') {
            $config->set('fonts.default', $font);
        }
    }

    protected function registerSketches(): void
    {
        // Resolving the registry loads deferred SketchesServiceProvider when needed.
        $this->container->make(SketchRegistry::class)->register(CanvasWindowDemo::class);
    }
}
