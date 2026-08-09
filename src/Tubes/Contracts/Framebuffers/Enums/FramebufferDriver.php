<?php

namespace ScrapyardIO\Tubes\Contracts\Framebuffers\Enums;

/**
 * Built-in managed framebuffer registration keys shipped by tubes.
 */
enum FramebufferDriver: string
{
    case FULL = 'full';
    case DIRTY = 'dirty';
    case PAGE = 'page';
}
