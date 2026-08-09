<?php

namespace ScrapyardIO\Tubes\Contracts\Framebuffers;

use InvalidArgumentException;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\BitDepth;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\BitOrder;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\Endianness;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\PageAxis;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\PixelFormat;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\ScanDirection;

/**
 * Describes how a block of pixel data is laid out in a host store or emit target.
 *
 * Only pixel format and bit depth are always required. The remaining facts are
 * situational and default to null when they do not apply to a given packing
 * family: bit order is for sub-byte (monochrome/planar) packing, endianness for
 * multi-byte pixels (TFT 16/18/24/32-bit), page axis only for paged monochrome
 * panels, and palette only for channel-sorted (multi-plane ePaper) surfaces.
 */
readonly class FormatSpec
{
    public function __construct(
        public PixelFormat $pixel_format,
        public BitDepth $bit_depth,
        public ScanDirection $scan_direction = ScanDirection::TOP_TO_BOTTOM,
        public ?BitOrder $bit_order = null,
        public ?Endianness $endianness = null,
        public ?PageAxis $page_axis = null,
        public ?ChannelPalette $palette = null,
    ) {}

    /**
     * Byte length of one WxH surface packed in this host layout (one Z layer).
     */
    public function bytesForSurface(int $width, int $height): int
    {
        if (($width < 1) || ($height < 1)) {
            throw new InvalidArgumentException(
                "Surface dimensions must be positive, got {$width}x{$height}."
            );
        }

        return match ($this->pixel_format) {
            PixelFormat::MONO_VERTICAL_PAGE => $width * intdiv($height + 7, 8),
            PixelFormat::MONO_HORIZONTAL => $height * intdiv($width + 7, 8),
            PixelFormat::PLANAR => $this->planarBytesForSurface($width, $height),
            PixelFormat::ROW_MAJOR => $this->rowMajorBytesForSurface($width, $height),
        };
    }

    protected function planarBytesForSurface(int $width, int $height): int
    {
        if (is_null($this->palette)) {
            throw new InvalidArgumentException(
                'PLANAR FormatSpec requires a ChannelPalette to size the host store.'
            );
        }

        $plane_bytes = $height * intdiv($width + 7, 8);

        return $plane_bytes * $this->palette->count();
    }

    protected function rowMajorBytesForSurface(int $width, int $height): int
    {
        $pixels = $width * $height;

        if ($this->bit_depth === BitDepth::B12) {
            // ST77xx COLOR12: two RGB444 pixels → three bytes (odd pixel padded).
            return intdiv($pixels + 1, 2) * 3;
        }

        $bytes_per_pixel = intdiv($this->bit_depth->value + 7, 8);

        return $pixels * $bytes_per_pixel;
    }
}
