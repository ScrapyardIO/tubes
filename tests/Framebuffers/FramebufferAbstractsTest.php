<?php

use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\BitDepth;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\Endianness;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\PixelFormat;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\RenderType;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Framebuffers\DeferredFramebuffer;
use ScrapyardIO\Tubes\Framebuffers\DirtyRegionsBuffer;
use ScrapyardIO\Tubes\Framebuffers\FullFramebuffer;
use ScrapyardIO\Tubes\Framebuffers\ManagedFramebuffer;

function rowMajorSpec(): FormatSpec
{
    return new FormatSpec(
        PixelFormat::ROW_MAJOR,
        BitDepth::B32,
        endianness: Endianness::MSB,
    );
}

test('Managed FullFramebuffer implements shared Framebuffer pixel API', function () {
    $fb = FullFramebuffer::sized(8, 4, rowMajorSpec());

    expect($fb)->toBeInstanceOf(ManagedFramebuffer::class)
        ->and($fb->viewportWidth())->toBe(8)
        ->and($fb->viewportHeight())->toBe(4)
        ->and($fb->hostFormat()->bit_depth)->toBe(BitDepth::B32)
        ->and($fb->preservesContentsOnPresent())->toBeTrue();

    $fb->clear()->setPixel(1, 1, 0xFF00FF00)->fill(0x11);
    expect($fb->getPixel(0, 0))->toBe(0x11);
});

test('DeferredFramebuffer abstract requires present and isHeadless', function () {
    $fb = new class(4, 4, rowMajorSpec()) extends DeferredFramebuffer
    {
        /** @var array<int, array<int, int>> */
        private array $grid = [];

        public function getPixel(int $x, int $y): int
        {
            return $this->grid[$y][$x] ?? 0;
        }

        public function setPixel(int $x, int $y, int $value): static
        {
            $this->grid[$y][$x] = $value;

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

        public function dump(?int $layer = null): string
        {
            return '';
        }

        public function flush(FormatSpec $spec, bool $as_array = false): string|array
        {
            return '';
        }

        public function present(): static
        {
            return $this;
        }

        public function isHeadless(): bool
        {
            return true;
        }
    };

    expect($fb->isHeadless())->toBeTrue()
        ->and($fb->present())->toBe($fb)
        ->and($fb->clear()->setPixel(0, 0, 7)->getPixel(0, 0))->toBe(7);
});

test('DirtyRegionsBuffer flush reindexes sparse dirty keys and emits PARTIAL', function () {
    $fb = DirtyRegionsBuffer::sized(8, 8, rowMajorSpec());

    $fb->setPixel(1, 1, 0x11223344);
    $fb->setPixel(6, 6, 0x55667788);

    // Simulate coalesce holes (unset without reindex) — flush must not assume key 0.
    $ref = new ReflectionClass($fb);
    $prop = $ref->getProperty('dirty_regions');
    $prop->setAccessible(true);
    $prop->setValue($fb, [
        2 => [1, 1, 1, 1],
        5 => [6, 6, 6, 6],
    ]);

    $frames = $fb->flush(rowMajorSpec(), as_array: true);

    expect($frames)->toHaveCount(2)
        ->and($frames[0]->render_type)->toBe(RenderType::PARTIAL)
        ->and($frames[1]->render_type)->toBe(RenderType::PARTIAL);
});

test('matching FormatSpec flush returns host dump bytes unchanged', function () {
    $spec = new FormatSpec(
        PixelFormat::ROW_MAJOR,
        BitDepth::B16,
        endianness: Endianness::MSB,
    );
    $fb = DirtyRegionsBuffer::sized(2, 2, $spec);

    $poison = "\xDE\xAD\xBE\xEF\xCA\xFE\xBA\xBE";
    $store = $fb->pixelStore();
    $pixels = new ReflectionProperty($store, 'pixels');
    $pixels->setAccessible(true);
    $pixels->setValue($store, $poison);

    $fb->markAllDirty();
    $frames = $fb->flush($spec, as_array: true);

    expect($frames)->toHaveCount(1)
        ->and($frames[0]->render_type)->toBe(RenderType::FULL)
        ->and($frames[0]->raw_data)->toBe($poison)
        ->and($frames[0]->raw_data)->toBe($fb->dump());
});

test('B16 ROW_MAJOR fill packs RGBA once into MSB RGB565 bytes', function () {
    $spec = new FormatSpec(
        PixelFormat::ROW_MAJOR,
        BitDepth::B16,
        endianness: Endianness::MSB,
    );
    $fb = FullFramebuffer::sized(2, 1, $spec);

    $fb->fill(0xFF0000FF); // pure red 0xRRGGBBAA → 0xF800

    expect(bin2hex($fb->dump()))->toBe('f800f800')
        ->and($fb->getPixel(0, 0))->toBe(0xF800)
        ->and($fb->getPixel(1, 0))->toBe(0xF800);
});

test('B16 black 0x000000FF packs to 0x0000 not RGB565 blue 0x00FF', function () {
    $spec = new FormatSpec(
        PixelFormat::ROW_MAJOR,
        BitDepth::B16,
        endianness: Endianness::MSB,
    );
    $fb = FullFramebuffer::sized(1, 1, $spec);

    // Color::black()->pack() — must not use <=0xFFFF native passthrough.
    $fb->fill(0x000000FF);

    expect(bin2hex($fb->dump()))->toBe('0000')
        ->and($fb->getPixel(0, 0))->toBe(0);
});

test('DirtyRegionsBuffer coalesces many markDirty rects once at flush', function () {
    $spec = new FormatSpec(
        PixelFormat::ROW_MAJOR,
        BitDepth::B16,
        endianness: Endianness::MSB,
    );
    $fb = DirtyRegionsBuffer::sized(8, 8, $spec);

    // Adjacent marks (circle VLine style) must become one PARTIAL at flush.
    for ($x = 1; $x <= 4; $x++) {
        $fb->setSegment($x, 2, 1, 3, 0xFF0000FF);
    }

    $frames = $fb->flush($spec, as_array: true);

    expect($frames)->toHaveCount(1)
        ->and($frames[0]->render_type)->toBe(RenderType::PARTIAL)
        ->and($frames[0]->origin_x)->toBe(1)
        ->and($frames[0]->origin_y)->toBe(2)
        ->and($frames[0]->width)->toBe(4)
        ->and($frames[0]->height)->toBe(3);
});
