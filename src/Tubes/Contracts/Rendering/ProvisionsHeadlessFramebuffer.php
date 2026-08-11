<?php

namespace ScrapyardIO\Tubes\Contracts\Rendering;

use ScrapyardIO\Tubes\Contracts\Framebuffers\DeferredFramebuffer;

/**
 * Engine Renderer2D that can provision a headless Deferred framebuffer for PanelIC.
 *
 * Window canvases bind buffers via WindowHandler; PanelIC engine lane calls this
 * instead of accepting an injected Deferred (which would invite window-attached
 * surfaces). Host FormatSpec is the engine’s native layout; PanelIC::present()
 * flushes to the IC’s FormatSpec (transcode).
 */
interface ProvisionsHeadlessFramebuffer
{
    /**
     * Off-screen Deferred sized for a panel (must report {@see DeferredFramebuffer::isHeadless()} true).
     */
    public function provisionHeadlessFramebuffer(int $width, int $height): DeferredFramebuffer;
}
