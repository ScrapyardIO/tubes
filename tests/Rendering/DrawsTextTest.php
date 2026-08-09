<?php

use ScrapyardIO\Tubes\Contracts\Framebuffers\DamageGranularity;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\BitDepth;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\Endianness;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\PixelFormat;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Framebuffer as FramebufferContract;
use ScrapyardIO\Tubes\Rendering\Concerns\DrawsText;
use ScrapyardIO\Tubes\Rendering\Renderer2D;

function textStubFramebuffer(int $w = 64, int $h = 32): FramebufferContract
{
    $spec = new FormatSpec(PixelFormat::ROW_MAJOR, BitDepth::B32, endianness: Endianness::MSB);

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
            return $this;
        }

        public function setRegion(array $coordinates, int $value): static
        {
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
            return $target;
        }

        public function blitFrom(FramebufferContract $source, int $offset_x = 0, int $offset_y = 0): FramebufferContract
        {
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

test('DrawsText prints classic glyphs into the borrowed framebuffer', function () {
    $renderer = new class extends Renderer2D
    {
        use DrawsText;

        public function drawPixel(int $x, int $y, int $color): static
        {
            $this->framebuffer()->setPixel($x, $y, $color);

            return $this;
        }

        public function fillRect(int $x, int $y, int $w, int $h, int $color): static
        {
            $this->framebuffer()->setSegment($x, $y, $w, $h, $color);

            return $this;
        }
    };

    $fb = textStubFramebuffer();
    $renderer->setFramebuffer($fb);
    $renderer->setTextColor(0xFFFFFFFF)->setCursor(0, 0)->print('A');

    $ink = 0;
    for ($y = 0; $y < 8; $y++) {
        for ($x = 0; $x < 6; $x++) {
            if ($fb->getPixel($x, $y) !== 0) {
                $ink++;
            }
        }
    }

    expect($ink)->toBeGreaterThan(0);
});
