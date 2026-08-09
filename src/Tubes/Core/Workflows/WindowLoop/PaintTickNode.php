<?php

namespace ScrapyardIO\Tubes\Core\Workflows\WindowLoop;

use Closure;
use Fabricate\Sketches\Flow\Node;
use Fabricate\Sketches\SketchRunner;
use ScrapyardIO\Tubes\Canvas\OSWindow;

/**
 * One cooperative frame: paint callback → present → poll.
 *
 * Actions: continue | stop
 *
 * Expected shared keys:
 * - window: OSWindow
 * - paint: callable(OSWindow $window, int $tick): void
 * - tick: int
 * - runner?: SketchRunner (cooperative stop)
 * - should_stop?: callable(): bool (e.g. nested Flow SIGINT flag)
 */
class PaintTickNode extends Node
{
    public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
    {
        $window = $shared['window'] ?? null;
        if (! ($window instanceof OSWindow)) {
            $shared['error'] = 'PaintTickNode requires shared[window] to be an OSWindow.';

            return 'stop';
        }

        if ($this->stopRequested($shared) || $window->shouldClose()) {
            return 'stop';
        }

        $tick = is_int($shared['tick'] ?? null) ? $shared['tick'] : 0;
        $paint = $shared['paint'] ?? null;

        if ($paint instanceof Closure || is_callable($paint)) {
            $paint($window, $tick);
        }

        $window->present()->pollEvents();
        $shared['tick'] = $tick + 1;

        if ($this->stopRequested($shared) || $window->shouldClose()) {
            return 'stop';
        }

        return 'continue';
    }

    /**
     * @param  array<string, mixed>  $shared
     */
    protected function stopRequested(array $shared): bool
    {
        $runner = $shared['runner'] ?? null;
        if ($runner instanceof SketchRunner && $runner->shouldStop()) {
            return true;
        }

        $shouldStop = $shared['should_stop'] ?? null;
        if (is_callable($shouldStop) && $shouldStop()) {
            return true;
        }

        return false;
    }
}
