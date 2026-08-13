<?php

namespace ScrapyardIO\Tubes\Canvas;

use ScrapyardIO\Tubes\Contracts\Framebuffers\DeferredFramebuffer;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Framebuffer as FramebufferContract;
use ScrapyardIO\Tubes\Windows\WindowHandler;

/**
 * OS window canvas — thin wrapper over a companion {@see WindowHandler}.
 *
 * The handler owns FormatSpec + DeferredFramebuffer for its engine. Present
 * goes through the handler's native path (no PHP flush / FormatSpec remux).
 */
class OSWindow extends Canvas
{
    public function __construct(
        protected WindowHandler $handler,
    ) {}

    public function handler(): WindowHandler
    {
        return $this->handler;
    }

    public function title(): string
    {
        return $this->handler->title();
    }

    public function width(): int
    {
        return $this->handler->width();
    }

    public function height(): int
    {
        return $this->handler->height();
    }

    public function formatSpec(): FormatSpec
    {
        return $this->handler->formatSpec();
    }

    public function isOpen(): bool
    {
        return $this->handler->isOpen();
    }

    public function open(): static
    {
        $this->handler->open();

        return $this;
    }

    public function framebuffer(): FramebufferContract
    {
        return $this->handler->framebuffer();
    }

    /**
     * Deferred buffer bound to this window (same instance as {@see framebuffer()}).
     */
    public function deferredFramebuffer(): DeferredFramebuffer
    {
        return $this->handler->framebuffer();
    }

    public function present(): static
    {
        $this->handler->present();

        return $this;
    }

    public function pollEvents(): static
    {
        $this->handler->pollEvents();

        return $this;
    }

    /**
     * Keyboard / pad input should be ignored when this is false.
     */
    public function hasInputFocus(): bool
    {
        return $this->handler->hasInputFocus();
    }

    public function shouldClose(): bool
    {
        return $this->handler->isOpen() && $this->handler->shouldClose();
    }

    public function close(): static
    {
        $this->handler->close();

        return $this;
    }
}
