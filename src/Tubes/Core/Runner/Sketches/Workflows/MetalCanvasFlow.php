<?php

namespace ScrapyardIO\Tubes\Core\Runner\Sketches\Workflows;

use Fabricate\Sketches\Flow\AsyncFlow;
use Fabricate\Sketches\Flow\Node;
use ScrapyardIO\Tubes\Core\Workflows\WindowLoop\CloseWindowNode;
use ScrapyardIO\Tubes\Core\Workflows\WindowLoop\OpenPanelNode;
use ScrapyardIO\Tubes\Core\Workflows\WindowLoop\OpenWindowNode;
use ScrapyardIO\Tubes\Core\Workflows\WindowLoop\PaintTickNode;

/**
 * Canvas demo loop: open → (physics → paint → pace)* → close.
 *
 * Target frame rate from shared['fps'] (default 60) via {@see FramePaceNode}.
 * Open node is a window or panel depending on {@see make()} / {@see makePanel()}.
 */
class MetalCanvasFlow extends AsyncFlow
{
    public static function make(): self
    {
        return self::fromOpen(new OpenWindowNode);
    }

    public static function makePanel(): self
    {
        return self::fromOpen(new OpenPanelNode);
    }

    protected static function fromOpen(Node $open): self
    {
        $physics = new BallPhysicsNode(concurrencyDriver: 'fiber');
        $tick = new PaintTickNode;
        $pace = new FramePaceNode;
        $close = new CloseWindowNode;
        $failClose = new CloseWindowNode;

        $open->next($physics);
        $open->on('fail')->next($failClose);

        $physics->next($tick);
        $tick->next($pace, 'continue');
        $tick->next($close, 'stop');
        $pace->next($physics);

        return new self($open);
    }
}
