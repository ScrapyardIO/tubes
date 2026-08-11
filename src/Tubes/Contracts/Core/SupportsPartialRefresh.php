<?php

namespace ScrapyardIO\Tubes\Contracts\Core;

/**
 * Marker: this sink can accept PARTIAL {@see \ScrapyardIO\Tubes\Contracts\Framebuffers\DumpedBuffer}
 * frames (address-window / page-window transmit) without requiring a full-surface rewrite.
 *
 * Panel ICs that implement this (ST77xx, GC9A01, SSD1306, SH1106, …) pair with
 * DirtyRegionsBuffer / PageSegmentBuffer so CPU present transmits only dirty regions.
 *
 * OSWindow / GPU engines may also implement this later when their present path
 * can push sub-rects; absence means callers should treat present as full-surface.
 */
interface SupportsPartialRefresh
{
}
