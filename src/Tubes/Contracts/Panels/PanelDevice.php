<?php

namespace ScrapyardIO\Tubes\Contracts\Panels;

use ScrapyardIO\Tubes\Contracts\Framebuffers\DumpedBuffer;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;

/**
 * Chip-facing surface a tubes {@see \ScrapyardIO\Tubes\Canvas\PanelIC} wraps.
 *
 * DOSR display ICs implement a qualification marker ({@see MonochromeDisplay}
 * or {@see FullColorDisplay}) plus this shared transmit API.
 */
interface PanelDevice
{
    public function width(): int;

    public function height(): int;

    public function formatSpec(): FormatSpec;

    public function transmit(DumpedBuffer $frame): void;

    public function close(): void;
}
