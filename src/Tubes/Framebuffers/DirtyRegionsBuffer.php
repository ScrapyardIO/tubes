<?php

namespace ScrapyardIO\Tubes\Framebuffers;

use RuntimeException;
use ScrapyardIO\Tubes\Contracts\Framebuffers\DamageGranularity;
use ScrapyardIO\Tubes\Contracts\Framebuffers\DumpedBuffer;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\PixelFormat;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\RenderType;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Contracts\Framebuffers\PixelStore as PixelStoreContract;

/**
 * Tracks coalesced dirty rectangles; flush emits PARTIAL region dumps.
 *
 * Host packing must be ROW_MAJOR (same constraint as 0.6 DirtyRegionsBuffer).
 */
final class DirtyRegionsBuffer extends ManagedFramebuffer
{
    /**
     * Inclusive [left, top, right, bottom] bounds.
     *
     * @var array<int, array{0: int, 1: int, 2: int, 3: int}>
     */
    protected array $dirty_regions = [];

    public function __construct(PixelStoreContract $pixel_store)
    {
        parent::__construct($pixel_store);
        $this->guardRowMajor();
    }

    public function setPixel(int $x, int $y, int $value): static
    {
        $this->pixel_store->setPixel($x, $y, $value);

        if (
            ($x >= 0) && ($y >= 0)
            && ($x < $this->viewportWidth())
            && ($y < $this->viewportHeight())
        ) {
            $this->markDirty($x, $y, $x, $y);
        }

        return $this;
    }

    public function setSegment(int $x, int $y, int $width, int $height, int $color): static
    {
        $this->pixel_store->setSegment($x, $y, $width, $height, $color);

        if (($width > 0) && ($height > 0)) {
            $this->markDirty($x, $y, ($x + $width) - 1, ($y + $height) - 1);
        }

        return $this;
    }

    public function clear(): static
    {
        parent::clear();
        $this->markAllDirty();

        return $this;
    }

    public function fill(int $color): static
    {
        parent::fill($color);
        $this->markAllDirty();

        return $this;
    }

    public function markAllDirty(): static
    {
        $this->dirty_regions = [[
            0,
            0,
            $this->viewportWidth() - 1,
            $this->viewportHeight() - 1,
        ]];

        return $this;
    }

    public function damageGranularity(): DamageGranularity
    {
        return DamageGranularity::pixel(
            $this->viewportWidth(),
            $this->viewportHeight(),
        );
    }

    /**
     * @return array<int, DumpedBuffer>
     */
    public function flush(FormatSpec $spec, bool $as_array = false): string|array
    {
        if ($this->dirty_regions === []) {
            return [];
        }

        if (! $this->formatSpecsMatch($this->hostFormat(), $spec)) {
            throw new RuntimeException(
                'DirtyRegionsBuffer flush FormatSpec must match the host store (region transcode not implemented).'
            );
        }

        $updates = [];

        foreach ($this->dirty_regions as [$left, $top, $right, $bottom]) {
            $width = ($right - $left) + 1;
            $height = ($bottom - $top) + 1;

            $updates[] = new DumpedBuffer(
                RenderType::PARTIAL,
                $spec,
                $this->dumpRegion($left, $top, $width, $height),
                origin_x: $left,
                origin_y: $top,
                width: $width,
                height: $height,
            );
        }

        $this->dirty_regions = [];

        return $updates;
    }

    protected function markDirty(int $left, int $top, int $right, int $bottom): void
    {
        $width = $this->viewportWidth();
        $height = $this->viewportHeight();

        $left = max(0, $left);
        $top = max(0, $top);
        $right = min($width - 1, $right);
        $bottom = min($height - 1, $bottom);

        if (($left > $right) || ($top > $bottom)) {
            return;
        }

        $merged = true;

        while ($merged) {
            $merged = false;

            foreach ($this->dirty_regions as $index => [$region_left, $region_top, $region_right, $region_bottom]) {
                $touches = ($left <= $region_right + 1) && ($region_left <= $right + 1)
                    && ($top <= $region_bottom + 1) && ($region_top <= $bottom + 1);

                if ($touches) {
                    $left = min($left, $region_left);
                    $top = min($top, $region_top);
                    $right = max($right, $region_right);
                    $bottom = max($bottom, $region_bottom);

                    unset($this->dirty_regions[$index]);
                    $merged = true;
                }
            }
        }

        $this->dirty_regions[] = [$left, $top, $right, $bottom];
    }

    protected function guardRowMajor(): void
    {
        if ($this->hostFormat()->pixel_format !== PixelFormat::ROW_MAJOR) {
            throw new RuntimeException(
                "DirtyRegionsBuffer only supports ROW_MAJOR hosts, got {$this->hostFormat()->pixel_format->value}."
            );
        }
    }
}
