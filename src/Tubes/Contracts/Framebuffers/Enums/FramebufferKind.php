<?php

namespace ScrapyardIO\Tubes\Contracts\Framebuffers\Enums;

/**
 * Registration lane for framebuffer strategies.
 */
enum FramebufferKind: string
{
    case MANAGED = 'managed';
    case DEFERRED = 'deferred';
}
