<?php

namespace ScrapyardIO\Tubes\Canvas;

use ScrapyardIO\Tubes\Contracts\Framebuffers\Framebuffer as FramebufferContract;

/**
 * Abstract 2D presentation surface (OS Window | IC Panel).
 *
 * Draw code type-hints {@see FramebufferContract} from {@see framebuffer()}.
 * Present ownership stays on the canvas / handler — not the renderer.
 */
abstract class Canvas
{
    abstract public function width(): int;

    abstract public function height(): int;

    /**
     * Pixel medium for this presentation surface.
     */
    abstract public function framebuffer(): FramebufferContract;

    /**
     * Push pixels to the sink (native present or IC transmit).
     */
    abstract public function present(): static;
}
