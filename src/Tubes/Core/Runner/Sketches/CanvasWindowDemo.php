<?php

namespace ScrapyardIO\Tubes\Core\Runner\Sketches;

use Fabricate\Contracts\Sketches\Attributes\Sketch as SketchAttribute;
use Fabricate\Contracts\Sketches\SketchLoopResult;
use Fabricate\Sketches\Sketch;
use ScrapyardIO\Tubes\Canvas\OSWindow;
use ScrapyardIO\Tubes\Core\MagicAliases\Font;
use ScrapyardIO\Tubes\Core\MagicAliases\Window;
use ScrapyardIO\Tubes\Core\Runner\Sketches\Support\MetalCanvasHud;
use ScrapyardIO\Tubes\Core\Runner\Sketches\Workflows\MetalCanvasFlow;
use ScrapyardIO\Tubes\Rendering\Renderer2D;
use ScrapyardIO\Tubes\Rendering\SoftRenderer2D;
use ScrapyardIO\Tubes\Windows\WindowException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Visible Canvas: WindowHandler + DeferredFramebuffer + Renderer2D ball + HUD text.
 *
 * Physics runs on {@see Workflows\BallPhysicsNode} (AsyncNode / Concurrency fiber).
 * Paint uses Renderer2D only (no raw framebuffer draw). On OSWindow drivers with
 * Human Input, left-clicking the ball gives a small acceleration boost for 3 seconds.
 *
 * Window defaults come from tubes.canvas_profiles.windows.canvas-window-demo
 * (override with --profile / CLI driver / geometry flags).
 *
 *   ./runner canvas-window-demo
 *   ./runner canvas-window-demo open-gl
 *   ./runner canvas-window-demo vulkan
 *   ./runner canvas-window-demo cuda
 *   ./runner canvas-window-demo sdl3
 *   ./runner canvas-window-demo --profile=metal-canvas --fps=60
 */
#[SketchAttribute('canvas-window-demo')]
class CanvasWindowDemo extends Sketch
{
    protected string $description = 'Canvas Renderer2D + AsyncNode ball physics + mouse click boost — profile from tubes.canvas_profiles.windows; Ctrl-C or close window to stop';

    /**
     * @var list<string>
     */
    protected array $drivers = ['metal', 'open-gl', 'vulkan', 'cuda', 'sdl3'];

    protected bool $ran = false;

    protected bool $stopRequested = false;

    protected ?Renderer2D $renderer = null;

    protected float $measuredFps = 0.0;

    protected ?int $lastPaintNs = null;

    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'driver',
            InputArgument::OPTIONAL,
            'Optional window driver override (metal|open-gl|vulkan|cuda|sdl3). Default: profile driver.',
        );

        $command->addOption(
            'profile',
            null,
            InputOption::VALUE_REQUIRED,
            'Window profile under tubes.canvas_profiles.windows',
            'canvas-window-demo',
        );

        $command->addOption(
            'title',
            null,
            InputOption::VALUE_REQUIRED,
            'Optional OS window title override (default: profile title)',
        );

        $command->addOption(
            'width',
            'W',
            InputOption::VALUE_REQUIRED,
            'Optional window width override (default: profile width)',
        );

        $command->addOption(
            'height',
            'H',
            InputOption::VALUE_REQUIRED,
            'Optional window height override (default: profile height)',
        );

        $command->addOption(
            'fps',
            null,
            InputOption::VALUE_REQUIRED,
            'Target frames per second',
            '60',
        );
    }

    public function boot(): void
    {
        if (! extension_loaded('pcntl')) {
            return;
        }

        pcntl_async_signals(true);

        $requestStop = function (): void {
            $this->stopRequested = true;
        };

        pcntl_signal(SIGINT, $requestStop);
        pcntl_signal(SIGTERM, $requestStop);
    }

    public function loop(): SketchLoopResult
    {
        if ($this->ran) {
            return SketchLoopResult::STOP;
        }

        $this->ran = true;

        $profile = $this->resolveProfile();
        if (is_null($profile)) {
            return SketchLoopResult::STOP;
        }

        try {
            $pending = Window::profile($profile);
        } catch (WindowException $exception) {
            $this->error($exception->getMessage());

            return SketchLoopResult::STOP;
        }

        $driver = $this->resolveDriverOverride() ?? $pending->driver();
        if (is_null($this->assertDriverSupported($driver))) {
            return SketchLoopResult::STOP;
        }

        $titleOverride = $this->resolveOptionalString('title');
        $widthOverride = $this->resolveOptionalPositiveInt('width');
        $heightOverride = $this->resolveOptionalPositiveInt('height');
        $fps = $this->resolvePositiveInt('fps', 60);

        if ($widthOverride === false || $heightOverride === false || is_null($fps)) {
            return SketchLoopResult::STOP;
        }

        $title = $titleOverride ?? $pending->titleValue();
        $width = $widthOverride ?? $pending->widthValue();
        $height = $heightOverride ?? $pending->heightValue();

        $this->renderer = $this->resolveRenderer($driver);
        $this->measuredFps = (float) $fps;
        $this->lastPaintNs = null;
        $this->info("Opening profile [{$profile}] → [{$driver}] {$width}x{$height} @{$fps}fps — {$title} via ".$this->renderer::class);

        $radius = 24;
        $shared = [
            'profile' => $profile,
            'fps' => $fps,
            'restitution' => 0.85,
            'should_stop' => fn (): bool => $this->stopRequested,
            'ball' => [
                'x' => $width / 2,
                'y' => $height / 2,
                // Not proportional to 4:3 — avoids immediate corner-to-corner orbit.
                'vx' => 7.1,
                'vy' => 2.4,
                'ax' => 0.0,
                'ay' => 0.0,
                'r' => $radius,
            ],
            'paint' => null,
        ];

        if ($driver !== $pending->driver()) {
            $shared['driver'] = $driver;
        }

        if (! is_null($titleOverride)) {
            $shared['title'] = $titleOverride;
        }

        if (! is_null($widthOverride)) {
            $shared['width'] = $widthOverride;
        }

        if (! is_null($heightOverride)) {
            $shared['height'] = $heightOverride;
        }

        $shared['paint'] = function (OSWindow $window, int $tick) use (&$shared): void {
            $this->paint($window, $tick, $shared);
        };

        MetalCanvasFlow::make()->run($shared);

        $this->renderer?->unsetFramebuffer();
        $this->renderer = null;

        if (isset($shared['error']) && is_string($shared['error'])) {
            $this->error($shared['error']);
        } else {
            $openedDriver = is_string($shared['driver'] ?? null) ? $shared['driver'] : $driver;
            $this->info("Canvas [{$openedDriver}] stopped after ".(int) ($shared['tick'] ?? 0).' ticks.');
        }

        return SketchLoopResult::STOP;
    }

    /**
     * Renderer2D frame — ball pose from AsyncNode physics; HUD via DrawsText.
     *
     * @param  array<string, mixed>  $shared
     */
    protected function paint(OSWindow $window, int $tick, array $shared): void
    {
        $renderer = $this->renderer;
        if (is_null($renderer)) {
            return;
        }

        $now = hrtime(true);
        if (! is_null($this->lastPaintNs)) {
            $this->measuredFps = MetalCanvasHud::blendFps($this->measuredFps, $now - $this->lastPaintNs);
        }
        $this->lastPaintNs = $now;

        $ball = is_array($shared['ball'] ?? null) ? $shared['ball'] : [];
        $targetFps = is_int($shared['fps'] ?? null) ? $shared['fps'] : 60;

        $vx = (float) ($ball['vx'] ?? 0.0);
        $vy = (float) ($ball['vy'] ?? 0.0);
        $ax = (float) ($ball['ax'] ?? 0.0);
        $ay = (float) ($ball['ay'] ?? 0.0);
        $speed = MetalCanvasHud::speed($vx, $vy);
        $accent = MetalCanvasHud::accentForSpeed($speed);

        $fb = $window->framebuffer();
        $renderer->setFramebuffer($fb);

        try {
            $bg = 0x141820FF;
            $hud = 0xE8ECF0FF;
            $dot = 0xFFFFFFFF;

            $r = max(1, (int) ($ball['r'] ?? 24));
            $x = (int) round((float) ($ball['x'] ?? $window->width() / 2));
            $y = (int) round((float) ($ball['y'] ?? $window->height() / 2));

            $renderer
                ->fill($bg)
                ->fillCircle($x, $y, $r, $accent)
                ->drawCircle($x, $y, $r, $dot)
                ->drawPixel((int) ($window->width() / 2), (int) ($window->height() / 2), $dot);

            $boostRemaining = is_float($shared['click_boost_remaining'] ?? null)
                || is_int($shared['click_boost_remaining'] ?? null)
                ? (float) $shared['click_boost_remaining']
                : 0.0;

            $this->paintHud($renderer, MetalCanvasHud::lines(
                $vx,
                $vy,
                $ax,
                $ay,
                $this->measuredFps,
                $accent,
                $targetFps,
                $boostRemaining,
            ), $hud, $bg);
        } finally {
            $renderer->unsetFramebuffer();
        }
    }

    /**
     * @param  list<string>  $lines
     */
    protected function paintHud(Renderer2D $renderer, array $lines, int $fg, int $bg): void
    {
        $slugs = MetalCanvasHud::resolveFontSlugs(
            static fn (string $slug): bool => Font::hasFont($slug),
        );

        // Prefer a single HUD face so baseline math stays consistent across lines.
        $fontSlug = $slugs['value'] ?? $slugs['label'] ?? null;
        if (is_null($fontSlug) || $fontSlug === '') {
            $fontSlug = Font::defaultFont();
        }

        $fontArg = $fontSlug === Font::defaultFont() ? null : $fontSlug;
        $fontInstance = (! is_null($fontArg) && Font::hasFont($fontArg))
            ? Font::font($fontArg)
            : Font::driver();

        $renderer
            ->setFont($fontArg)
            ->setTextSize(1)
            ->setTextWrap(false)
            ->setTextColor($fg, $bg)
            ->setCursor(8, MetalCanvasHud::hudBaselineY($fontInstance));

        foreach ($lines as $line) {
            $renderer->println($line);
        }

        // Restore default font so later frames do not accumulate baseline shifts unexpectedly.
        $renderer->setFont(null);
    }

    protected function resolveRenderer(string $driver): Renderer2D
    {
        /** @var array<string, class-string<Renderer2D>> $map */
        $map = [
            'metal' => 'Microscrap\\GFX\\Metal\\MetalRenderer2D',
            'open-gl' => 'Microscrap\\GFX\\OGX\\OpenGLRenderer2D',
            'vulkan' => 'Microscrap\\GFX\\Vulkan\\VulkanRenderer2D',
            'cuda' => 'Microscrap\\GFX\\CUDA\\CudaGPURenderer2D',
            'sdl3' => 'Microscrap\\GFX\\SDL3\\SDL3Renderer2D',
        ];

        $class = $map[$driver] ?? null;
        if (! is_null($class) && class_exists($class)) {
            return new $class;
        }

        return new SoftRenderer2D;
    }

    protected function resolveProfile(): ?string
    {
        $raw = $this->option('profile');
        $profile = is_string($raw) ? trim($raw) : '';

        if ($profile === '') {
            $this->error('Option --profile must be a non-empty canvas window profile slug.');

            return null;
        }

        return $profile;
    }

    protected function resolveDriverOverride(): ?string
    {
        $raw = $this->argument('driver');
        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }

        $driver = strtolower(trim($raw));
        if ($driver === 'opengl') {
            $driver = 'open-gl';
        }

        return $this->assertDriverSupported($driver);
    }

    protected function assertDriverSupported(string $driver): ?string
    {
        if (! in_array($driver, $this->drivers, true)) {
            $this->error('Unsupported driver ['.$driver.']. Use: '.implode('|', $this->drivers));

            return null;
        }

        return $driver;
    }

    protected function resolveOptionalString(string $option): ?string
    {
        $raw = $this->option($option);

        if (! is_string($raw) || $raw === '') {
            return null;
        }

        return $raw;
    }

    /**
     * @return int|null|false null = unset, false = invalid, int = override
     */
    protected function resolveOptionalPositiveInt(string $option): int|false|null
    {
        $raw = $this->option($option);

        if (is_null($raw) || $raw === '') {
            return null;
        }

        if (! is_numeric($raw)) {
            $this->error("Option --{$option} must be a positive integer.");

            return false;
        }

        $value = (int) $raw;

        if ($value < 1) {
            $this->error("Option --{$option} must be >= 1.");

            return false;
        }

        return $value;
    }

    protected function resolvePositiveInt(string $option, int $default): ?int
    {
        $raw = $this->option($option);

        if (is_null($raw) || $raw === '') {
            return $default;
        }

        if (! is_numeric($raw)) {
            $this->error("Option --{$option} must be a positive integer.");

            return null;
        }

        $value = (int) $raw;

        if ($value < 1) {
            $this->error("Option --{$option} must be >= 1.");

            return null;
        }

        return $value;
    }
}
