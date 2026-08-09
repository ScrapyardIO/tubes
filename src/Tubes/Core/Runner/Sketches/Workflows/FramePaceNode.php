<?php

namespace ScrapyardIO\Tubes\Core\Runner\Sketches\Workflows;

use Fabricate\Sketches\Flow\Node;

/**
 * Cap the frame loop to shared['fps'] (default 60).
 *
 * Expects shared['frame_t0'] (hrtime ns) set at the start of the frame
 * (BallPhysicsNode::prepAsync). Sleeps the remainder of the frame budget.
 */
class FramePaceNode extends Node
{
    public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
    {
        $fps = is_int($shared['fps'] ?? null) ? $shared['fps'] : 60;
        $fps = max(1, $fps);
        $budgetNs = intdiv(1_000_000_000, $fps);

        $started = is_int($shared['frame_t0'] ?? null)
            ? $shared['frame_t0']
            : hrtime(true);

        $elapsed = hrtime(true) - $started;
        $remaining = $budgetNs - $elapsed;

        if ($remaining > 0) {
            // usleep takes microseconds
            usleep((int) intdiv($remaining, 1000));
        }

        return 'default';
    }
}
