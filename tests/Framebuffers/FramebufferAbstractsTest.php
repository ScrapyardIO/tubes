<?php

use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\BitDepth;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\Endianness;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\PixelFormat;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Framebuffers\DeferredFramebuffer;
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
