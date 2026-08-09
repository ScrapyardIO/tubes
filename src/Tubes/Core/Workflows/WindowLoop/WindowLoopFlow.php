<?php

namespace ScrapyardIO\Tubes\Core\Workflows\WindowLoop;

use Fabricate\Sketches\Flow\Flow;

/**
 * Canvas present loop: open → paint/present/poll (self-loop) → close.
 *
 * Shared bag:
 * - driver, title, width, height
 * - paint: callable(OSWindow, int $tick): void
 * - runner?: SketchRunner for cooperative SIGINT stop
 *
 * Evolution hook: swap `paint` for a rendering-engine tick later without
 * changing this graph.
 */
class WindowLoopFlow extends Flow
{
    public static function make(): self
    {
        $open = new OpenWindowNode;
        $tick = new PaintTickNode;
        $close = new CloseWindowNode;
        $failClose = new CloseWindowNode;

        $open->next($tick);
        $open->on('fail')->next($failClose);

        $tick->next($tick, 'continue');
        $tick->next($close, 'stop');

        return new self($open);
    }
}
