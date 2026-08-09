<?php

namespace ScrapyardIO\Tubes\Contracts\Rendering;

use RuntimeException;

class RenderingException extends RuntimeException
{
    public static function framebufferNotBound(): static
    {
        return new static('Renderer has no framebuffer bound. Call setFramebuffer() before drawing.');
    }

    public static function notImplemented(string $method): static
    {
        return new static("Renderer2D::{$method}() is not implemented by this graphics driver.");
    }
}
