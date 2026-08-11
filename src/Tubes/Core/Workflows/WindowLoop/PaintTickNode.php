<?php

namespace ScrapyardIO\Tubes\Core\Workflows\WindowLoop;

use Closure;
use Fabricate\Sketches\Flow\Node;
use Fabricate\Sketches\SketchRunner;
use ScrapyardIO\Tubes\Canvas\Canvas;
use ScrapyardIO\Tubes\Canvas\OSWindow;

/**
 * One cooperative frame: paint callback → present → (window poll).
 *
 * Actions: continue | stop
 *
 * Expected shared keys:
 * - canvas: Canvas (preferred) — or window: OSWindow (BC)
 * - paint: callable(Canvas $canvas, int $tick): void
 * - tick: int
 * - runner?: SketchRunner (cooperative stop)
 * - should_stop?: callable(): bool (e.g. nested Flow SIGINT flag)
 */
class PaintTickNode extends Node
{
    public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
    {
        $canvas = $shared['canvas'] ?? $shared['window'] ?? null;
        if (! ($canvas instanceof Canvas)) {
            $shared['error'] = 'PaintTickNode requires shared[canvas] (or shared[window]) to be a Canvas.';

            return 'stop';
        }

        if ($this->stopRequested($shared) || $this->surfaceWantsClose($canvas)) {
            return 'stop';
        }

        $tick = is_int($shared['tick'] ?? null) ? $shared['tick'] : 0;
        $paint = $shared['paint'] ?? null;

        $workStarted = hrtime(true);

        if ($paint instanceof Closure || is_callable($paint)) {
            $paint($canvas, $tick);
        }

        $paintEnded = hrtime(true);
        $canvas->present();
        $presentEnded = hrtime(true);

        // Wall time of paint+present (excludes FramePace sleep) — HUD FPS must use this.
        $shared['paint_ns'] = $paintEnded - $workStarted;
        $shared['present_ns'] = $presentEnded - $paintEnded;
        $shared['work_ns'] = $presentEnded - $workStarted;

        if ($canvas instanceof OSWindow) {
            $canvas->pollEvents();
        }

        $shared['tick'] = $tick + 1;

        if ($this->stopRequested($shared) || $this->surfaceWantsClose($canvas)) {
            return 'stop';
        }

        return 'continue';
    }

    protected function surfaceWantsClose(Canvas $canvas): bool
    {
        return $canvas instanceof OSWindow && $canvas->shouldClose();
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
