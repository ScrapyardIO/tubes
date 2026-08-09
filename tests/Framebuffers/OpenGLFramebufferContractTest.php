<?php

use ScrapyardIO\Tubes\Contracts\Framebuffers\DeferredFramebuffer;
use ScrapyardIO\Tubes\Contracts\Framebuffers\OpenGLFramebuffer;

test('OpenGLFramebuffer is a DeferredFramebuffer marker', function () {
    expect(is_a(OpenGLFramebuffer::class, DeferredFramebuffer::class, true))->toBeTrue();
});

test('DeferredFramebuffer contract owns present and isHeadless', function () {
    expect(method_exists(DeferredFramebuffer::class, 'present'))->toBeTrue();
    expect(method_exists(DeferredFramebuffer::class, 'isHeadless'))->toBeTrue();
});
