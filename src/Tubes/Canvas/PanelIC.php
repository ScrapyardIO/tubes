<?php

namespace ScrapyardIO\Tubes\Canvas;

use ScrapyardIO\Tubes\Contracts\Framebuffers\Framebuffer as FramebufferContract;
use ScrapyardIO\Tubes\Contracts\Framebuffers\ManagedFramebuffer as ManagedFramebufferContract;

/**
 * IC panel canvas (sibling of {@see OSWindow}).
 *
 * Uses a Managed framebuffer for pack/transmit. Device / circuit wiring lands
 * when IC Panel restore continues — this pass only locks the canvas shape.
 */
abstract class PanelIC extends Canvas
{
    public function __construct(
        protected ManagedFramebufferContract $framebuffer,
    ) {}

    public function width(): int
    {
        return $this->framebuffer->viewportWidth();
    }

    public function height(): int
    {
        return $this->framebuffer->viewportHeight();
    }

    public function framebuffer(): FramebufferContract
    {
        return $this->framebuffer;
    }

    /**
     * Transmit packed bytes to the panel IC. Subclasses implement device flush.
     */
    abstract public function present(): static;
}
