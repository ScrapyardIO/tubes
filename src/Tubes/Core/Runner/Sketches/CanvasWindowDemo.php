<?php

namespace ScrapyardIO\Tubes\Core\Runner\Sketches;

use Fabricate\Contracts\Sketches\Attributes\Sketch as SketchAttribute;
use Fabricate\Contracts\Sketches\SketchLoopResult;
use Fabricate\Sketches\Sketch;
use ScrapyardIO\Tubes\Canvas\Canvas;
use ScrapyardIO\Tubes\Canvas\PanelIC;
use ScrapyardIO\Tubes\Core\Enums\CanvasProfileKind;
use ScrapyardIO\Tubes\Core\MagicAliases\Font;
use ScrapyardIO\Tubes\Core\MagicAliases\Panel;
use ScrapyardIO\Tubes\Core\MagicAliases\Window;
use ScrapyardIO\Tubes\Core\Runner\Sketches\Support\MetalCanvasHud;
use ScrapyardIO\Tubes\Core\Runner\Sketches\Workflows\MetalCanvasFlow;
use ScrapyardIO\Tubes\Core\Support\CanvasProfiles;
use ScrapyardIO\Tubes\Panels\PanelException;
use ScrapyardIO\Tubes\Rendering\Renderer2D;
use ScrapyardIO\Tubes\Windows\WindowException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Visible Canvas: PanelIC (default) or OSWindow + Renderer2D ball + HUD text.
 *
 * With no CLI driver / --profile, opens tubes.defaults.canvas (any windows.* or panels.* profile).
 * Pass a window driver or --profile=… to force a window profile override.
 *
 * Physics runs on {@see Workflows\BallPhysicsNode} (AsyncNode / Concurrency fiber).
 * Paint uses Renderer2D only. On OSWindow drivers with Human Input, left-clicking
 * the ball gives a small acceleration boost for 3 seconds.
 *
 *   ./runner canvas-window-demo
 *   ./runner canvas-window-demo open-gl
 *   ./runner canvas-window-demo vulkan
 *   ./runner canvas-window-demo cuda
 *   ./runner canvas-window-demo sdl3
 *   ./runner canvas-window-demo --profile=canvas-window-demo --fps=60
 */
#[SketchAttribute('canvas-window-demo')]
class CanvasWindowDemo extends Sketch
{
    protected string $description = 'Canvas Renderer2D + AsyncNode ball physics — default tubes.defaults.canvas (window or panel profile); Ctrl-C (or close window) to stop';

    /**
     * @var list<string>
     */
    protected array $drivers = ['metal', 'open-gl', 'vulkan', 'cuda', 'sdl3'];

    protected bool $ran = false;

    protected bool $stopRequested = false;

    protected ?Renderer2D $renderer = null;

    protected float $measuredFps = 0.0;

    protected ?int $lastPaintNs = null;

    /** CPU partial path: surface cleared once, then erase previous ball/HUD only. */
    protected bool $surfacePrimed = false;

    protected ?int $lastBallX = null;

    protected ?int $lastBallY = null;

    protected ?int $lastBallR = null;

    /**
     * Previous HUD lines (for glyph-box erase on partial CPU paints).
     *
     * @var list<string>
     */
    protected array $lastHudLines = [];

    protected int $lastHudBaselineY = 8;

    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'driver',
            InputArgument::OPTIONAL,
            'Optional window driver override (metal|open-gl|vulkan|cuda|sdl3). Omitting driver+profile uses tubes.defaults.canvas.',
        );

        $command->addOption(
            'profile',
            null,
            InputOption::VALUE_REQUIRED,
            'Window profile under tubes.canvas_profiles.windows (forces window path when set)',
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

        if ($this->wantsDefaultCanvas()) {
            return $this->runDefaultCanvas();
        }

        return $this->runWindowCanvas($this->resolveProfile());
    }

    /**
     * No driver argument and no --profile → tubes.defaults.canvas (window or panel profile).
     */
    protected function wantsDefaultCanvas(): bool
    {
        $driverRaw = $this->argument('driver');
        $hasDriver = is_string($driverRaw) && trim($driverRaw) !== '';

        $profileRaw = $this->option('profile');
        $hasProfile = is_string($profileRaw) && trim($profileRaw) !== '';

        return ! $hasDriver && ! $hasProfile;
    }

    protected function runDefaultCanvas(): SketchLoopResult
    {
        $slug = $this->resolveDefaultCanvasProfile();
        if (is_null($slug)) {
            return SketchLoopResult::STOP;
        }

        try {
            [$kind] = CanvasProfiles::locate($slug);
        } catch (\InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return SketchLoopResult::STOP;
        }

        return match ($kind) {
            CanvasProfileKind::PANELS => $this->runPanelCanvas($slug),
            CanvasProfileKind::WINDOWS => $this->runWindowCanvas($slug),
        };
    }

    protected function runPanelCanvas(string $panelProfile): SketchLoopResult
    {
        $fps = $this->resolvePositiveInt('fps', 60);
        if (is_null($fps)) {
            return SketchLoopResult::STOP;
        }

        try {
            $panel = Panel::profile($panelProfile);
        } catch (PanelException $exception) {
            $this->error($exception->getMessage());

            return SketchLoopResult::STOP;
        }

        $this->renderer = $panel->renderer();
        $width = $panel->width();
        $height = $panel->height();
        $this->measuredFps = (float) $fps;
        $this->lastPaintNs = null;
        $this->resetPartialPaintState();

        $this->info(
            "Opening default canvas panel [{$panelProfile}] {$width}x{$height} @{$fps}fps via ".$this->renderer::class
        );

        $shared = $this->baseShared($width, $height, $fps);
        $shared['canvas'] = $panel;
        $shared['panel_profile'] = $panelProfile;
        $shared['paint'] = function (Canvas $canvas, int $tick) use (&$shared): void {
            $this->paint($canvas, $tick, $shared);
        };

        MetalCanvasFlow::makePanel()->run($shared);

        $this->renderer?->unsetFramebuffer();
        $this->renderer = null;

        if (isset($shared['error']) && is_string($shared['error'])) {
            $this->error($shared['error']);
        } else {
            $this->info("Panel canvas [{$panelProfile}] stopped after ".(int) ($shared['tick'] ?? 0).' ticks.');
        }

        return SketchLoopResult::STOP;
    }

    protected function runWindowCanvas(?string $profile): SketchLoopResult
    {
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
        $this->resetPartialPaintState();
        $this->info("Opening profile [{$profile}] → [{$driver}] {$width}x{$height} @{$fps}fps — {$title} via ".$this->renderer::class);

        $shared = $this->baseShared($width, $height, $fps);
        $shared['profile'] = $profile;

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

        $shared['paint'] = function (Canvas $canvas, int $tick) use (&$shared): void {
            $this->paint($canvas, $tick, $shared);
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
     * @return array<string, mixed>
     */
    protected function baseShared(int $width, int $height, int $fps): array
    {
        $radius = 24;

        return [
            'fps' => $fps,
            'restitution' => 0.85,
            'should_stop' => fn (): bool => $this->stopRequested,
            'width' => $width,
            'height' => $height,
            'ball' => [
                'x' => $width / 2,
                'y' => $height / 2,
                // px/s — not proportional to 4:3 (avoids immediate corner-to-corner orbit).
                // ~7.1 / 2.4 px/frame at 60fps.
                'vx' => 426.0,
                'vy' => 144.0,
                'ax' => 0.0,
                'ay' => 0.0,
                'r' => $radius,
            ],
            'paint' => null,
        ];
    }

    /**
     * Renderer2D frame — ball pose from AsyncNode physics; HUD via DrawsText.
     *
     * CPU PanelIC + SupportsPartialRefresh + dirty/page FB: prime once, then erase
     * previous ball/HUD damage only so flush emits PARTIAL (not FULL every frame).
     * OSWindow / full-surface FB keeps a clear each frame.
     *
     * @param  array<string, mixed>  $shared
     */
    protected function paint(Canvas $canvas, int $tick, array $shared): void
    {
        $renderer = $this->renderer;
        if (is_null($renderer)) {
            return;
        }

        // Prefer paint+present work_ns (excludes FramePace sleep). Fall back to wall Δt.
        $workNs = $shared['work_ns'] ?? null;
        if (is_int($workNs) && $workNs > 0) {
            $this->measuredFps = MetalCanvasHud::blendFps($this->measuredFps, $workNs);
        } else {
            $now = hrtime(true);
            if (! is_null($this->lastPaintNs)) {
                $this->measuredFps = MetalCanvasHud::blendFps($this->measuredFps, $now - $this->lastPaintNs);
            }
            $this->lastPaintNs = $now;
        }

        $ball = is_array($shared['ball'] ?? null) ? $shared['ball'] : [];
        $targetFps = is_int($shared['fps'] ?? null) ? $shared['fps'] : 60;

        $vx = (float) ($ball['vx'] ?? 0.0);
        $vy = (float) ($ball['vy'] ?? 0.0);
        $ax = (float) ($ball['ax'] ?? 0.0);
        $ay = (float) ($ball['ay'] ?? 0.0);
        $speed = MetalCanvasHud::speed($vx, $vy);
        $accent = MetalCanvasHud::accentForSpeed($speed);

        $fb = $canvas->framebuffer();
        $renderer->setFramebuffer($fb);

        try {
            // Draw colours are always 0xRRGGBBAA. PanelIC::present() packs to the IC FormatSpec.
            $bg = 0x141820FF;
            $hud = 0xE8ECF0FF;
            $dot = 0xFFFFFFFF;

            $r = max(1, (int) ($ball['r'] ?? 24));
            $x = (int) round((float) ($ball['x'] ?? $canvas->width() / 2));
            $y = (int) round((float) ($ball['y'] ?? $canvas->height() / 2));

            $partial = $this->usesCpuPartialPaint($canvas);
            $paintHud = (! $partial) || ($tick % 6 === 0);

            if (! $partial || ! $this->surfacePrimed) {
                $renderer->fill($bg);
                $this->surfacePrimed = true;
                $this->lastBallX = null;
                $this->lastBallY = null;
                $this->lastBallR = null;
                $this->lastHudLines = [];
            } else {
                if (! is_null($this->lastBallX) && ! is_null($this->lastBallY) && ! is_null($this->lastBallR)) {
                    if ($this->lastBallX !== $x || $this->lastBallY !== $y || $this->lastBallR !== $r) {
                        $renderer->fillCircle(
                            $this->lastBallX,
                            $this->lastBallY,
                            $this->lastBallR + 1,
                            $bg,
                        );
                    }
                }

                if ($paintHud) {
                    $this->eraseHudGlyphBoxes($renderer, $bg);
                }
            }

            $renderer
                ->fillCircle($x, $y, $r, $accent)
                ->drawCircle($x, $y, $r, $dot)
                ->drawPixel((int) ($canvas->width() / 2), (int) ($canvas->height() / 2), $dot);

            $this->lastBallX = $x;
            $this->lastBallY = $y;
            $this->lastBallR = $r;

            $boostRemaining = is_float($shared['click_boost_remaining'] ?? null)
                || is_int($shared['click_boost_remaining'] ?? null)
                ? (float) $shared['click_boost_remaining']
                : 0.0;

            $hudLines = MetalCanvasHud::lines(
                $vx,
                $vy,
                $ax,
                $ay,
                $this->measuredFps,
                $accent,
                $targetFps,
                $boostRemaining,
            );

            // ~10Hz HUD on partial CPU path — digit churn must not enlarge dirty SPI.
            if ($paintHud) {
                $this->paintHud($renderer, $hudLines, $hud, $bg);
                $this->lastHudLines = $hudLines;
            }
        } finally {
            // PanelIC owns the renderer↔FB binding; leave it set after paint.
            if (! $canvas instanceof PanelIC) {
                $renderer->unsetFramebuffer();
            }
        }
    }

    /**
     * CPU PanelIC only: chip SupportsPartialRefresh + FB damage smaller than full surface.
     */
    protected function usesCpuPartialPaint(Canvas $canvas): bool
    {
        return $canvas instanceof PanelIC && $canvas->supportsPartialRefresh();
    }

    protected function resetPartialPaintState(): void
    {
        $this->surfacePrimed = false;
        $this->lastBallX = null;
        $this->lastBallY = null;
        $this->lastBallR = null;
        $this->lastHudLines = [];
        $this->lastHudBaselineY = 8;
    }

    /**
     * Erase previous HUD glyph boxes only (never a full HUD band — that coalesces
     * with the ball into huge SPI rects).
     */
    protected function eraseHudGlyphBoxes(Renderer2D $renderer, int $bg): void
    {
        if ($this->lastHudLines === []) {
            return;
        }

        $cellW = 6;
        $cellH = 8;
        $y = $this->lastHudBaselineY;

        foreach ($this->lastHudLines as $line) {
            $w = max($cellW, strlen($line) * $cellW);
            $renderer->fillRect(8, max(0, $y - $cellH + 2), $w + 2, $cellH + 2, $bg);
            $y += $cellH + 2;
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

        $baseline = MetalCanvasHud::hudBaselineY($fontInstance);
        $this->lastHudBaselineY = $baseline;

        $renderer
            ->setFont($fontArg)
            ->setTextSize(1)
            ->setTextWrap(false)
            ->setTextColor($fg, $bg)
            ->setCursor(8, $baseline);

        foreach ($lines as $line) {
            $renderer->println($line);
        }

        // Restore default font so later frames do not accumulate baseline shifts unexpectedly.
        $renderer->setFont(null);
    }

    protected function resolveDefaultCanvasProfile(): ?string
    {
        $raw = config('tubes.defaults.canvas');
        $profile = is_string($raw) ? trim($raw) : '';

        if ($profile === '') {
            $this->error(
                'No default canvas configured. Set tubes.defaults.canvas to a canvas_profiles '
                .'windows.* or panels.* slug (e.g. st7796-front, canvas-window-demo), '
                .'or pass a window driver / --profile=….'
            );

            return null;
        }

        return $profile;
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

        throw new \RuntimeException(
            "Canvas window driver [{$driver}] requires its engine Renderer2D companion "
            .'(tubes ships DrawingAPI/Renderer2D contracts only — no CPU renderer fallback).'
        );
    }

    protected function resolveProfile(): ?string
    {
        $raw = $this->option('profile');
        $profile = is_string($raw) ? trim($raw) : '';

        if ($profile === '') {
            return 'canvas-window-demo';
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
