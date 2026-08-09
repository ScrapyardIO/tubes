<?php

namespace ScrapyardIO\Tubes\Rendering;

use ScrapyardIO\Tubes\Contracts\Framebuffers\Framebuffer;
use ScrapyardIO\Tubes\Contracts\Rendering\RenderingException;

/**
 * Base renderer — borrows a Canvas/handler framebuffer by reference (no pixel copy).
 *
 * Presentation owns the buffer; the renderer only holds a reference for draw calls.
 */
abstract class Renderer
{
    protected ?Framebuffer $framebuffer = null;

    /**
     * Bind the presentation framebuffer by reference (same instance, no RAM copy).
     */
    public function setFramebuffer(Framebuffer &$framebuffer): static
    {
        $this->framebuffer = &$framebuffer;

        return $this;
    }

    /**
     * Drop the borrowed framebuffer binding.
     */
    public function unsetFramebuffer(): static
    {
        $this->framebuffer = null;

        return $this;
    }

    public function hasFramebuffer(): bool
    {
        return ! is_null($this->framebuffer);
    }

    /**
     * Bound framebuffer (throws when unset).
     */
    public function framebuffer(): Framebuffer
    {
        if (is_null($this->framebuffer)) {
            throw RenderingException::framebufferNotBound();
        }

        return $this->framebuffer;
    }

    /**
     * Logical draw width (from bound framebuffer).
     */
    public function width(): int
    {
        return $this->hasFramebuffer() ? $this->framebuffer()->viewportWidth() : 0;
    }

    /**
     * Logical draw height (from bound framebuffer).
     */
    public function height(): int
    {
        return $this->hasFramebuffer() ? $this->framebuffer()->viewportHeight() : 0;
    }
}
