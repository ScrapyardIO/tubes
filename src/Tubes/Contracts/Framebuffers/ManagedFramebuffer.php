<?php

namespace ScrapyardIO\Tubes\Contracts\Framebuffers;

interface ManagedFramebuffer extends Framebuffer
{
    /**
     * Force the next flush to cover the whole surface (or every page/region unit).
     */
    public function markAllDirty(): static;
}
