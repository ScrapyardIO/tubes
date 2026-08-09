<?php

namespace ScrapyardIO\Tubes\Contracts\Framebuffers;

interface PixelStore
{
    public function width(): int;

    public function height(): int;

    /**
     * Layer count (Z). One means a plain 2D surface.
     */
    public function z(): int;

    /**
     * Working / host packing for the binary blob.
     */
    public function hostFormat(): FormatSpec;

    /**
     * Bytes in one Z layer under the host FormatSpec.
     */
    public function layerByteLength(): int;

    /**
     * Total allocated bytes (layerByteLength × z).
     */
    public function byteLength(): int;

    /**
     * Raw host bytes. Pass a layer index for one slab; omit for the full stack.
     */
    public function dump(?int $layer = null): string;

    /**
     * Zero the full store, or a single layer.
     */
    public function clear(?int $layer = null): static;

    /**
     * Fill the full store or a single layer with a packed colour word.
     */
    public function fill(int $color, ?int $layer = null): static;

    public function getPixel(int $x, int $y, int $layer = 0): int;

    public function setPixel(int $x, int $y, int $color, int $layer = 0): static;

    /**
     * @param  array<int, array{0: int, 1: int, 2: int}>  $pixels  Each entry is [x, y, color]
     */
    public function setPixels(array $pixels, int $layer = 0): static;

    /**
     * Fill a rectangle. Off-surface cells are clipped.
     */
    public function setSegment(int $x, int $y, int $width, int $height, int $color, int $layer = 0): static;
}
