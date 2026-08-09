<?php

use ScrapyardIO\Tubes\Contracts\Framebuffers\DamageGranularity;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\BitDepth;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\Endianness;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\PixelFormat;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Framebuffer as FramebufferContract;
use ScrapyardIO\Tubes\Contracts\Rendering\DrawingAPI;
use ScrapyardIO\Tubes\Contracts\Rendering\RenderingException;
use ScrapyardIO\Tubes\Rendering\Renderer2D;

function renderingRowMajorSpec(): FormatSpec
{
    return new FormatSpec(
        PixelFormat::ROW_MAJOR,
        BitDepth::B32,
        endianness: Endianness::MSB,
    );
}

/**
 * Minimal Framebuffer stand-in (avoids Managed/PixelStore in this unit).
 */
function stubFramebuffer(int $w = 4, int $h = 4): FramebufferContract
{
    $spec = renderingRowMajorSpec();

    return new class($w, $h, $spec) implements FramebufferContract
    {
        /** @var array<int, array<int, int>> */
        private array $grid = [];

        public function __construct(
            private int $width,
            private int $height,
            private FormatSpec $host_format,
        ) {}

        public function viewportWidth(): int
        {
            return $this->width;
        }

        public function viewportHeight(): int
        {
            return $this->height;
        }

        public function hostFormat(): FormatSpec
        {
            return $this->host_format;
        }

        public function getPixel(int $x, int $y): int
        {
            return $this->grid[$y][$x] ?? 0;
        }

        public function setPixel(int $x, int $y, int $value): static
        {
            $this->grid[$y][$x] = $value;

            return $this;
        }

        public function setPixels(array $pixels): static
        {
            foreach ($pixels as [$x, $y, $value]) {
                $this->setPixel($x, $y, $value);
            }

            return $this;
        }

        public function setRegion(array $coordinates, int $value): static
        {
            foreach ($coordinates as [$x, $y]) {
                $this->setPixel($x, $y, $value);
            }

            return $this;
        }

        public function setSegment(int $x, int $y, int $width, int $height, int $color): static
        {
            for ($row = $y; $row < $y + $height; $row++) {
                for ($col = $x; $col < $x + $width; $col++) {
                    $this->setPixel($col, $row, $color);
                }
            }

            return $this;
        }

        public function clear(): static
        {
            return $this->fill(0);
        }

        public function fill(int $color): static
        {
            return $this->setSegment(0, 0, $this->width, $this->height, $color);
        }

        public function blitTo(FramebufferContract $target, int $offset_x = 0, int $offset_y = 0): FramebufferContract
        {
            return $target->blitFrom($this, $offset_x, $offset_y);
        }

        public function blitFrom(FramebufferContract $source, int $offset_x = 0, int $offset_y = 0): FramebufferContract
        {
            for ($y = 0; $y < $source->viewportHeight(); $y++) {
                for ($x = 0; $x < $source->viewportWidth(); $x++) {
                    $this->setPixel($offset_x + $x, $offset_y + $y, $source->getPixel($x, $y));
                }
            }

            return $this;
        }

        public function dump(?int $layer = null): string
        {
            return '';
        }

        public function flush(FormatSpec $spec, bool $as_array = false): string|array
        {
            return $as_array ? [] : '';
        }

        public function damageGranularity(): DamageGranularity
        {
            return DamageGranularity::wholeSurface($this->width, $this->height);
        }

        public function preservesContentsOnPresent(): bool
        {
            return false;
        }
    };
}

/**
 * Minimal concrete Renderer2D for bind/draw plumbing tests.
 */
function stubRenderer2D(): Renderer2D
{
    return new class extends Renderer2D
    {
        public function drawPixel(int $x, int $y, int $color): static
        {
            $this->framebuffer()->setPixel($x, $y, $color);

            return $this;
        }

        public function fill(int $color): static
        {
            $this->framebuffer()->fill($color);

            return $this;
        }
    };
}

test('Renderer2D implements DrawingAPI and borrows framebuffer by reference', function () {
    $renderer = stubRenderer2D();
    $fb = stubFramebuffer();

    expect($renderer)->toBeInstanceOf(DrawingAPI::class)
        ->and($renderer->hasFramebuffer())->toBeFalse();

    $renderer->setFramebuffer($fb);

    expect($renderer->hasFramebuffer())->toBeTrue()
        ->and($renderer->framebuffer())->toBe($fb);

    $renderer->drawPixel(1, 1, 0xFF112233);
    expect($fb->getPixel(1, 1))->toBe(0xFF112233);

    $renderer->unsetFramebuffer();
    expect($renderer->hasFramebuffer())->toBeFalse();
});

test('Renderer2D framebuffer() throws when unbound', function () {
    $renderer = stubRenderer2D();

    $renderer->framebuffer();
})->throws(RenderingException::class);

test('setFramebuffer keeps the same Framebuffer instance (no copy)', function () {
    $renderer = stubRenderer2D();
    $fb = stubFramebuffer(2, 2);

    $renderer->setFramebuffer($fb);
    $bound = $renderer->framebuffer();

    expect($bound)->toBeInstanceOf(FramebufferContract::class)
        ->and($bound)->toBe($fb)
        ->and(spl_object_id($bound))->toBe(spl_object_id($fb));
});

test('default Renderer2D draw methods throw notImplemented until gfx overrides', function () {
    $renderer = new class extends Renderer2D {};
    $fb = stubFramebuffer(2, 2);
    $renderer->setFramebuffer($fb);

    $renderer->drawPixel(0, 0, 1);
})->throws(RenderingException::class);
