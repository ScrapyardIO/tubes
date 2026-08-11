<?php

namespace ScrapyardIO\Tubes\Canvas;

use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Framebuffer as FramebufferContract;

/**
 * Abstract 2D presentation surface ({@see OSWindow} | {@see PanelIC}).
 *
 * Consumers should type-hint {@see Canvas} for nearly all use cases — they must
 * not care whether the sink is a window or an IC panel. Prefer:
 *
 *   $fb = $canvas->framebuffer();
 *   $renderer->setFramebuffer($fb);
 *   // draw…
 *   $canvas->present();
 *
 * Ask for {@see OSWindow} or {@see PanelIC} only when you need window events,
 * Circuit device access, panel lane pairing, etc.
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
     * Layout facts for the presentation sink (window host or IC emit target).
     */
    abstract public function formatSpec(): FormatSpec;

    /**
     * Push pixels to the sink (native window present or IC transmit).
     */
    abstract public function present(): static;

    /**
     * Release the presentation surface.
     */
    abstract public function close(): static;
}
