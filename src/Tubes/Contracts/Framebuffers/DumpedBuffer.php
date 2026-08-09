<?php

namespace ScrapyardIO\Tubes\Contracts\Framebuffers;

use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\RenderType;

/**
 * One flush payload: packed bytes already shaped for {@see $metadata}.
 */
readonly class DumpedBuffer
{
    public function __construct(
        public RenderType $render_type,
        public FormatSpec $metadata,
        public string $raw_data,
        public int $origin_x = 0,
        public int $origin_y = 0,
        public ?int $width = null,
        public ?int $height = null,
    ) {}
}
