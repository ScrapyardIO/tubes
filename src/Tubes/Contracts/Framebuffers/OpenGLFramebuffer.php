<?php

namespace ScrapyardIO\Tubes\Contracts\Framebuffers;

/**
 * Marker for OpenGL-context deferred buffers (e.g. microscrap/ogx).
 *
 * {@see present()} and {@see isHeadless()} live on {@see DeferredFramebuffer}.
 * Companions must not name a concrete class OpenGlFramebuffer on Darwin
 * (classname collision with the OpenGL extension).
 */
interface OpenGLFramebuffer extends DeferredFramebuffer
{
}
