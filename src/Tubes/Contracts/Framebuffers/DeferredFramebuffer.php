<?php

namespace ScrapyardIO\Tubes\Contracts\Framebuffers;

/**
 * Host-backed framebuffer lane (SDL / OpenGL / Metal / Vulkan / …).
 *
 * Pixels are owned by the engine, not a tubes {@see PixelStore}. Callers that
 * only put pixels should type-hint {@see Framebuffer}; use this lane when they
 * need {@see present()} / {@see isHeadless()}.
 */
interface DeferredFramebuffer extends Framebuffer
{
    /**
     * Push / swap the current drawable (engine-defined).
     */
    public function present(): static;

    /**
     * True when this instance owns a headless / off-screen surface rather than
     * borrowing an app-owned window drawable.
     */
    public function isHeadless(): bool;
}
