<?php

use ScrapyardIO\Tubes\Contracts\Core\SupportsPartialRefresh;
use ScrapyardIO\Tubes\Contracts\Framebuffers\DamageGranularity;
use ScrapyardIO\Tubes\Contracts\Framebuffers\DeferredFramebuffer as DeferredFramebufferContract;
use ScrapyardIO\Tubes\Contracts\Framebuffers\DumpedBuffer;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\BitDepth;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\BitOrder;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\Endianness;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\PageAxis;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\PixelFormat;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\RenderType;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Contracts\Panels\FullColorDisplay;
use ScrapyardIO\Tubes\Contracts\Panels\MonochromeDisplay;
use ScrapyardIO\Tubes\Contracts\Rendering\ProvisionsHeadlessFramebuffer;
use ScrapyardIO\Tubes\Framebuffers\DeferredFramebuffer;
use ScrapyardIO\Tubes\Framebuffers\FullFramebuffer;
use ScrapyardIO\Tubes\Framebuffers\PageSegmentBuffer;
use ScrapyardIO\Tubes\Panels\FullColorDisplay as FullColorPanel;
use ScrapyardIO\Tubes\Panels\MonochromeDisplay as MonochromePanel;
use ScrapyardIO\Tubes\Panels\PanelException;
use ScrapyardIO\Tubes\Panels\PanelManager;
use ScrapyardIO\Tubes\Panels\Support\PreferredManagedFramebuffer;
use ScrapyardIO\Tubes\Rendering\Renderer2D;

function fakeMonoSpec(): FormatSpec
{
    return new FormatSpec(
        PixelFormat::MONO_VERTICAL_PAGE,
        BitDepth::B1,
        bit_order: BitOrder::LSB_FIRST,
        page_axis: PageAxis::VERTICAL,
    );
}

function fakeColorSpec(): FormatSpec
{
    return new FormatSpec(
        PixelFormat::ROW_MAJOR,
        BitDepth::B16,
        endianness: Endianness::MSB,
    );
}

function fakeCpuRenderer(): Renderer2D
{
    return new class extends Renderer2D
    {
        public function drawPixel(int $x, int $y, int $color): static
        {
            $this->framebuffer()->setPixel($x, $y, $color);

            return $this;
        }
    };
}

function fakeEngineRenderer(?DeferredFramebuffer $provisioned = null): Renderer2D
{
    return new class($provisioned) extends Renderer2D implements ProvisionsHeadlessFramebuffer
    {
        public int $provisionCalls = 0;

        public function __construct(private ?DeferredFramebuffer $provisioned) {}

        public function provisionHeadlessFramebuffer(int $width, int $height): DeferredFramebufferContract
        {
            $this->provisionCalls++;

            if (! is_null($this->provisioned)) {
                return $this->provisioned;
            }

            return fakeDeferred($width, $height, fakeEngineHostSpec(), headless: true);
        }

        public function drawPixel(int $x, int $y, int $color): static
        {
            $this->framebuffer()->setPixel($x, $y, $color);

            return $this;
        }
    };
}

function fakeEngineHostSpec(): FormatSpec
{
    return new FormatSpec(
        PixelFormat::ROW_MAJOR,
        BitDepth::B32,
        endianness: Endianness::MSB,
    );
}

function fakeColorIc(int $width = 240, int $height = 240): FullColorDisplay
{
    return new class($width, $height) implements FullColorDisplay, SupportsPartialRefresh
    {
        /** @var list<DumpedBuffer> */
        public array $transmitted = [];

        public function __construct(
            private int $width,
            private int $height,
        ) {}

        public function width(): int
        {
            return $this->width;
        }

        public function height(): int
        {
            return $this->height;
        }

        public function formatSpec(): FormatSpec
        {
            return fakeColorSpec();
        }

        public function transmit(DumpedBuffer $frame): void
        {
            $this->transmitted[] = $frame;
        }

        public function close(): void {}
    };
}

function fakeMonoIc(int $width = 128, int $height = 64): MonochromeDisplay
{
    return new class($width, $height) implements MonochromeDisplay, SupportsPartialRefresh
    {
        /** @var list<DumpedBuffer> */
        public array $transmitted = [];

        public function __construct(
            private int $width,
            private int $height,
        ) {}

        public function width(): int
        {
            return $this->width;
        }

        public function height(): int
        {
            return $this->height;
        }

        public function formatSpec(): FormatSpec
        {
            return fakeMonoSpec();
        }

        public function transmit(DumpedBuffer $frame): void
        {
            $this->transmitted[] = $frame;
        }

        public function close(): void {}
    };
}

function fakeDeferred(int $width, int $height, FormatSpec $spec, bool $headless = true): DeferredFramebuffer
{
    return new class($width, $height, $spec, $headless) extends DeferredFramebuffer
    {
        public int $flushCalls = 0;

        public function __construct(
            int $width,
            int $height,
            FormatSpec $host_format,
            private bool $headless,
        ) {
            parent::__construct($width, $height, $host_format);
        }

        public function getPixel(int $x, int $y): int
        {
            return 0;
        }

        public function setPixel(int $x, int $y, int $value): static
        {
            return $this;
        }

        public function setSegment(int $x, int $y, int $width, int $height, int $color): static
        {
            return $this;
        }

        public function dump(?int $layer = null): string
        {
            return str_repeat("\0", 32);
        }

        public function flush(FormatSpec $spec, bool $as_array = false): string|array
        {
            $this->flushCalls++;
            $bytes = $this->dump();

            if (! $as_array) {
                return $bytes;
            }

            return [
                new DumpedBuffer(
                    RenderType::FULL,
                    $spec,
                    $bytes,
                    width: $this->viewportWidth(),
                    height: $this->viewportHeight(),
                ),
            ];
        }

        public function present(): static
        {
            return $this;
        }

        public function isHeadless(): bool
        {
            return $this->headless;
        }

        public function damageGranularity(): DamageGranularity
        {
            return DamageGranularity::wholeSurface(
                $this->viewportWidth(),
                $this->viewportHeight(),
            );
        }
    };
}

test('PreferredManagedFramebuffer picks page for MonochromeDisplay', function () {
    expect(PreferredManagedFramebuffer::defaultDriverFor(fakeMonoIc()))->toBe('page')
        ->and(PreferredManagedFramebuffer::for(fakeMonoIc()))->toBeInstanceOf(PageSegmentBuffer::class);
});

test('PreferredManagedFramebuffer picks dirty for FullColorDisplay', function () {
    $fb = PreferredManagedFramebuffer::for(fakeColorIc());

    expect(PreferredManagedFramebuffer::defaultDriverFor(fakeColorIc()))->toBe('dirty')
        ->and($fb)->toBeInstanceOf(\ScrapyardIO\Tubes\Framebuffers\DirtyRegionsBuffer::class)
        ->and($fb->hostFormat()->bit_depth)->toBe(BitDepth::B16);
});

test('MonochromeDisplay rejects non-page CPU framebuffer driver', function () {
    PreferredManagedFramebuffer::for(fakeMonoIc(), 'full');
})->throws(PanelException::class, 'page');

test('FullColorDisplay rejects page CPU framebuffer driver', function () {
    PreferredManagedFramebuffer::for(fakeColorIc(), 'page');
})->throws(PanelException::class, 'page');

test('MonochromeDisplay rejects injected FullFramebuffer on CPU lane', function () {
    $ic = fakeMonoIc();
    $full = FullFramebuffer::sized($ic->width(), $ic->height(), $ic->formatSpec());

    (new PanelManager)->make()->wrap($ic)->useFramebuffer($full)->renderer(fakeCpuRenderer())->create();
})->throws(PanelException::class, 'page');

test('FullColorDisplay rejects injected PageSegmentBuffer on CPU lane', function () {
    $ic = fakeColorIc();
    // Build a page buffer with a mono-looking store size — still rejected by contract.
    $page = PageSegmentBuffer::sized(128, 64, fakeMonoSpec());

    (new PanelManager)->make()->wrap($ic)->useFramebuffer($page)->renderer(fakeCpuRenderer())->create();
})->throws(PanelException::class, 'page');

test('CPU PanelIC requires Managed framebuffer + CPU renderer', function () {
    $manager = new PanelManager;
    $ic = fakeMonoIc();
    $pages = PreferredManagedFramebuffer::for($ic);
    $renderer = fakeCpuRenderer();

    $panel = $manager->make()->wrap($ic)->useFramebuffer($pages)->renderer($renderer)->create();

    expect($panel)->toBeInstanceOf(MonochromePanel::class)
        ->and($panel->framebuffer())->toBe($pages)
        ->and($panel->renderer())->toBe($renderer)
        ->and($panel->renderer()->framebuffer())->toBe($pages);
});

test('CPU PanelIC accepts framebuffer driver string + CPU renderer', function () {
    $panel = (new PanelManager)
        ->make()
        ->wrap(fakeColorIc())
        ->framebuffer('full')
        ->renderer(fakeCpuRenderer())
        ->create();

    expect($panel)->toBeInstanceOf(FullColorPanel::class)
        ->and($panel->managedFramebuffer())->toBeInstanceOf(FullFramebuffer::class);
});

test('CPU PanelIC without framebuffer throws', function () {
    (new PanelManager)->make()->wrap(fakeMonoIc())->renderer(fakeCpuRenderer())->create();
})->throws(PanelException::class, 'CPU PanelIC');

test('Engine PanelIC takes renderer only and provisions headless Deferred', function () {
    $ic = fakeColorIc(4, 4);
    $renderer = fakeEngineRenderer();

    $panel = (new PanelManager)->make()->wrap($ic)->renderer($renderer)->create();

    expect($panel->renderer())->toBe($renderer)
        ->and($renderer->provisionCalls)->toBe(1)
        ->and($panel->framebuffer())->toBeInstanceOf(DeferredFramebuffer::class)
        ->and($panel->framebuffer()->isHeadless())->toBeTrue()
        ->and($panel->framebuffer()->hostFormat()->bit_depth)->toBe(BitDepth::B32);
});

test('Engine PanelIC present flushes IC FormatSpec (transcode target)', function () {
    $ic = fakeColorIc(4, 4);
    $deferred = fakeDeferred(4, 4, fakeEngineHostSpec(), headless: true);
    $renderer = fakeEngineRenderer($deferred);

    $panel = (new PanelManager)->make()->wrap($ic)->renderer($renderer)->create();
    $panel->present();

    expect($deferred->flushCalls)->toBe(1)
        ->and($ic->transmitted)->toHaveCount(1)
        ->and($ic->transmitted[0]->metadata->bit_depth)->toBe(BitDepth::B16);
});

test('Engine renderer + useFramebuffer Managed is rejected', function () {
    $ic = fakeColorIc();
    $pages = PreferredManagedFramebuffer::for($ic);

    (new PanelManager)->make()
        ->wrap($ic)
        ->useFramebuffer($pages)
        ->renderer(fakeEngineRenderer())
        ->create();
})->throws(PanelException::class, 'renderer() only');

test('useFramebuffer rejects Deferred injection', function () {
    (new PanelManager)->make()
        ->wrap(fakeColorIc(4, 4))
        ->useFramebuffer(fakeDeferred(4, 4, fakeEngineHostSpec()))
        ->renderer(fakeCpuRenderer())
        ->create();
})->throws(PanelException::class, 'Managed');

test('CPU renderer + Deferred injection is rejected', function () {
    // Same as above — useFramebuffer only accepts Managed
    (new PanelManager)->make()
        ->wrap(fakeColorIc(4, 4))
        ->useFramebuffer(fakeDeferred(4, 4, fakeEngineHostSpec()))
        ->renderer(fakeCpuRenderer())
        ->create();
})->throws(PanelException::class);

test('PanelIC present transmits flushed dumps on CPU lane', function () {
    $ic = fakeColorIc(8, 8);
    $fb = PreferredManagedFramebuffer::for($ic);

    $panel = (new PanelManager)->make()->wrap($ic)->useFramebuffer($fb)->renderer(fakeCpuRenderer())->create();
    $panel->framebuffer()->fill(0xF800);
    $panel->present();

    expect($ic->transmitted)->not->toBeEmpty()
        ->and($ic->transmitted[0])->toBeInstanceOf(DumpedBuffer::class);
});

test('CPU FullColor PanelIC host matches IC; RGBA packs on write; present has no transcode', function () {
    $ic = fakeColorIc(2, 1);
    $panel = (new PanelManager)
        ->make()
        ->wrap($ic)
        ->framebuffer('dirty')
        ->renderer(fakeCpuRenderer())
        ->create();

    expect($panel->framebuffer()->hostFormat()->bit_depth)->toBe(BitDepth::B16);

    // Pure red 0xRRGGBBAA → packed to RGB565 at setPixel; flush dumps host bytes.
    $panel->framebuffer()->fill(0xFF0000FF);
    $panel->present();

    expect($ic->transmitted)->toHaveCount(1)
        ->and($ic->transmitted[0]->metadata->bit_depth)->toBe(BitDepth::B16)
        ->and(bin2hex($ic->transmitted[0]->raw_data))->toBe('f800f800');
});

test('CPU SupportsPartialRefresh color PanelIC transmits PARTIAL after local dirty', function () {
    $ic = fakeColorIc(32, 32);
    $panel = (new PanelManager)
        ->make()
        ->wrap($ic)
        ->framebuffer('dirty')
        ->renderer(fakeCpuRenderer())
        ->create();

    expect($panel->supportsPartialRefresh())->toBeTrue();

    $panel->framebuffer()->fill(0x000000FF);
    $panel->present();
    expect($ic->transmitted)->toHaveCount(1)
        ->and($ic->transmitted[0]->render_type)->toBe(RenderType::FULL);

    $ic->transmitted = [];
    $panel->framebuffer()->setSegment(10, 12, 5, 4, 0xFF0000FF);
    $panel->present();

    expect($ic->transmitted)->toHaveCount(1)
        ->and($ic->transmitted[0]->render_type)->toBe(RenderType::PARTIAL)
        ->and($ic->transmitted[0]->origin_x)->toBe(10)
        ->and($ic->transmitted[0]->origin_y)->toBe(12)
        ->and($ic->transmitted[0]->width)->toBe(5)
        ->and($ic->transmitted[0]->height)->toBe(4)
        ->and(strlen($ic->transmitted[0]->raw_data))->toBe(5 * 4 * 2);
});

test('CPU SupportsPartialRefresh mono PanelIC transmits PARTIAL page runs', function () {
    $ic = fakeMonoIc(16, 16);
    $panel = (new PanelManager)
        ->make()
        ->wrap($ic)
        ->framebuffer('page')
        ->renderer(fakeCpuRenderer())
        ->create();

    expect($panel->supportsPartialRefresh())->toBeTrue();

    $panel->framebuffer()->fill(0);
    $panel->present();
    $ic->transmitted = [];

    $panel->framebuffer()->setPixel(3, 9, 1); // page 1 only
    $panel->present();

    expect($ic->transmitted)->toHaveCount(1)
        ->and($ic->transmitted[0]->render_type)->toBe(RenderType::PARTIAL)
        ->and($ic->transmitted[0]->origin_y)->toBe(8)
        ->and($ic->transmitted[0]->height)->toBe(8);
});

test('PanelManager requires a renderer', function () {
    $ic = fakeMonoIc();
    (new PanelManager)->make()->wrap($ic)->useFramebuffer(PreferredManagedFramebuffer::for($ic))->create();
})->throws(PanelException::class, 'renderer');

test('Panel profile missing definition throws', function () {
    (new PanelManager)->profile('does-not-exist');
})->throws(PanelException::class);

test('Panel build without wrap or circuit throws', function () {
    (new PanelManager)->make()->renderer(fakeEngineRenderer())->create();
})->throws(PanelException::class);
