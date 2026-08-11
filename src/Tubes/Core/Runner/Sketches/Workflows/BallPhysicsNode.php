<?php

namespace ScrapyardIO\Tubes\Core\Runner\Sketches\Workflows;

use ScrapyardIO\Tubes\Core\Runner\Sketches\Support\MetalCanvasClickBoost;
use Fabricate\Sketches\Flow\AsyncNode;
use ScrapyardIO\Tubes\Canvas\Canvas;
use ScrapyardIO\Tubes\Canvas\OSWindow;
use ScrapyardIO\Tubes\HumanInput\EngineInput;
use ScrapyardIO\Tubes\HumanInput\Enums\MouseButton;
use ScrapyardIO\Tubes\Inputs\InputHandler;
use ScrapyardIO\Tubes\Windows\WindowHandler;

/**
 * Cooperative ball physics step (AsyncNode with Concurrency driver `fiber`).
 *
 * Reads/writes shared['ball'] = [x, y, vx, vy, ax, ay, r]. Units: position px,
 * velocity px/s, acceleration px/s². Integrates with measured delta time
 * (shared['dt'], seconds). Bounds from the open Canvas (window or panel).
 *
 * On wall hit, multiplies that axis speed by shared['restitution'] (energy loss).
 * Corner hits resolve the deeper axis only so the ball does not lock into a
 * diagonal corner-to-corner orbit. Stamps shared['frame_t0'] for {@see FramePaceNode}.
 * ax/ay are Δv/dt for the sketch HUD.
 *
 * When shared['window'] / shared['canvas'] is an {@see OSWindow} with a companion
 * {@see InputHandler}, left-click hits on the ball start a {@see MetalCanvasClickBoost}
 * window (3s). PanelIC surfaces have no engine mouse — boost stays inactive.
 */
class BallPhysicsNode extends AsyncNode
{
    public function prepAsync(mixed &$shared): mixed
    {
        $shared['frame_t0'] = hrtime(true);
        $dt = $this->resolveDeltaSeconds($shared);
        $shared['dt'] = $dt;

        $canvas = $shared['canvas'] ?? $shared['window'] ?? null;
        $width = $canvas instanceof Canvas
            ? $canvas->width()
            : (is_int($shared['width'] ?? null) ? $shared['width'] : 800);
        $height = $canvas instanceof Canvas
            ? $canvas->height()
            : (is_int($shared['height'] ?? null) ? $shared['height'] : 600);

        $ball = is_array($shared['ball'] ?? null) ? $shared['ball'] : [];
        $restitution = is_float($shared['restitution'] ?? null) || is_int($shared['restitution'] ?? null)
            ? (float) $shared['restitution']
            : 0.85;

        $vx = (float) ($ball['vx'] ?? 426.0);
        $vy = (float) ($ball['vy'] ?? 144.0);
        $now = microtime(true);

        $this->applyMouseClickBoost($shared, $ball, $now);

        $boostUntil = is_float($shared['click_boost_until'] ?? null)
            ? (float) $shared['click_boost_until']
            : 0.0;
        $boostActive = MetalCanvasClickBoost::isActive($boostUntil, $now);
        $facingX = is_float($shared['boost_facing_x'] ?? null) ? (float) $shared['boost_facing_x'] : $vx;
        $facingY = is_float($shared['boost_facing_y'] ?? null) ? (float) $shared['boost_facing_y'] : $vy;

        [$boostAx, $boostAy] = $boostActive
            ? MetalCanvasClickBoost::acceleration($vx, $vy, $facingX, $facingY)
            : [0.0, 0.0];

        if ($boostActive && hypot($vx, $vy) > 3.0) {
            $shared['boost_facing_x'] = $vx;
            $shared['boost_facing_y'] = $vy;
        }

        $shared['click_boost_remaining'] = MetalCanvasClickBoost::remainingSeconds($boostUntil, $now);

        return [
            'width' => $width,
            'height' => $height,
            'dt' => $dt,
            'restitution' => max(0.0, min(1.0, $restitution)),
            'ball' => [
                'x' => (float) ($ball['x'] ?? $width / 2),
                'y' => (float) ($ball['y'] ?? $height / 2),
                'vx' => $vx,
                'vy' => $vy,
                'ax' => (float) ($ball['ax'] ?? 0.0),
                'ay' => (float) ($ball['ay'] ?? 0.0),
                'r' => (int) ($ball['r'] ?? 24),
            ],
            'prev_vx' => $vx,
            'prev_vy' => $vy,
            'boost_ax' => $boostAx,
            'boost_ay' => $boostAy,
        ];
    }

    public function execAsync(mixed $prepRes): mixed
    {
        $width = (int) $prepRes['width'];
        $height = (int) $prepRes['height'];
        $dt = max(1.0 / 240.0, (float) ($prepRes['dt'] ?? (1.0 / 60.0)));
        $restitution = (float) $prepRes['restitution'];
        $ball = $prepRes['ball'];
        $prevVx = (float) ($prepRes['prev_vx'] ?? $ball['vx']);
        $prevVy = (float) ($prepRes['prev_vy'] ?? $ball['vy']);

        $r = max(1, (int) $ball['r']);
        $vx = (float) $ball['vx'] + ((float) ($prepRes['boost_ax'] ?? 0.0) * $dt);
        $vy = (float) $ball['vy'] + ((float) ($prepRes['boost_ay'] ?? 0.0) * $dt);
        $x = (float) $ball['x'] + ($vx * $dt);
        $y = (float) $ball['y'] + ($vy * $dt);

        $minX = (float) $r;
        $maxX = (float) max($r, $width - $r - 1);
        $minY = (float) $r;
        $maxY = (float) max($r, $height - $r - 1);

        $penLeft = $minX - $x;
        $penRight = $x - $maxX;
        $penTop = $minY - $y;
        $penBottom = $y - $maxY;

        $hitX = $penLeft > 0 || $penRight > 0;
        $hitY = $penTop > 0 || $penBottom > 0;

        if ($hitX && $hitY) {
            // Corner: bounce only the deeper penetration axis (breaks diagonal lock).
            $penX = max($penLeft, $penRight);
            $penY = max($penTop, $penBottom);

            if ($penX >= $penY) {
                [$x, $vx] = $this->bounceX($x, $vx, $minX, $maxX, $restitution);
            } else {
                [$y, $vy] = $this->bounceY($y, $vy, $minY, $maxY, $restitution);
            }
        } else {
            if ($hitX) {
                [$x, $vx] = $this->bounceX($x, $vx, $minX, $maxX, $restitution);
            }
            if ($hitY) {
                [$y, $vy] = $this->bounceY($y, $vy, $minY, $maxY, $restitution);
            }
        }

        // Clamp in case the unresolved axis still sits slightly outside after a corner hit.
        $x = max($minX, min($maxX, $x));
        $y = max($minY, min($maxY, $y));

        if (abs($vx) < 3.0) {
            $vx = 0.0;
        }
        if (abs($vy) < 3.0) {
            $vy = 0.0;
        }

        return [
            'x' => $x,
            'y' => $y,
            'vx' => $vx,
            'vy' => $vy,
            // Δv/dt (bounce / clamp / click boost) for the HUD — px/s².
            'ax' => ($vx - $prevVx) / $dt,
            'ay' => ($vy - $prevVy) / $dt,
            'r' => $r,
        ];
    }

    public function postAsync(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
    {
        $shared['ball'] = is_array($execRes) ? $execRes : $prepRes['ball'];

        return 'default';
    }

    /**
     * Seconds since last physics tick. First frame (or missing clock) uses 1/fps.
     * Optional shared['dt_override'] for tests (does not sticky-overwrite measured dt).
     * Resolved value is written to shared['dt']. Clamped to avoid spiral-of-death.
     *
     * @param  array<string, mixed>  $shared
     */
    protected function resolveDeltaSeconds(array &$shared): float
    {
        $fps = is_int($shared['fps'] ?? null) ? max(1, $shared['fps']) : 60;
        $fallback = 1.0 / $fps;

        if (isset($shared['dt_override']) && is_numeric($shared['dt_override']) && (float) $shared['dt_override'] > 0.0) {
            $dt = (float) $shared['dt_override'];
        } else {
            $nowNs = is_int($shared['frame_t0'] ?? null)
                ? $shared['frame_t0']
                : hrtime(true);
            $last = $shared['physics_last_t'] ?? null;

            $dt = is_int($last) && $last > 0
                ? ($nowNs - $last) / 1_000_000_000.0
                : $fallback;

            $shared['physics_last_t'] = $nowNs;
        }

        // ~4× target fps floor … 50ms ceiling (hitches don't teleport the ball).
        return max(1.0 / 240.0, min(0.05, $dt));
    }

    /**
     * @param  array<string, mixed>  $shared
     * @param  array<string, mixed>  $ball
     */
    protected function applyMouseClickBoost(array &$shared, array $ball, float $now): void
    {
        $engine = $this->engineInputFor($shared);
        if (is_null($engine)) {
            $shared['mouse_left_was_pressed'] = false;

            return;
        }

        $mouse = $engine->mouse();
        $pressed = ! is_null($mouse) && $mouse->isPressed(MouseButton::LEFT);
        $wasPressed = (bool) ($shared['mouse_left_was_pressed'] ?? false);
        $shared['mouse_left_was_pressed'] = $pressed;

        if (! $pressed || $wasPressed || is_null($mouse)) {
            return;
        }

        if (! MetalCanvasClickBoost::hitsBall($mouse->x(), $mouse->y(), $ball)) {
            return;
        }

        $shared['click_boost_until'] = MetalCanvasClickBoost::boostUntilFromNow($now);
        $shared['boost_facing_x'] = (float) ($ball['vx'] ?? 1.0);
        $shared['boost_facing_y'] = (float) ($ball['vy'] ?? 0.0);
    }

    /**
     * @param  array<string, mixed>  $shared
     */
    protected function engineInputFor(array &$shared): ?EngineInput
    {
        $existing = $shared['engine_input'] ?? null;
        if ($existing instanceof EngineInput) {
            return $existing;
        }

        $window = $shared['window'] ?? $shared['canvas'] ?? null;
        if (! ($window instanceof OSWindow)) {
            return null;
        }

        $handler = $window->handler();
        if (! $handler instanceof WindowHandler || ! method_exists($handler, 'inputHandler')) {
            return null;
        }

        $input = $handler->inputHandler();
        if (! $input instanceof InputHandler) {
            return null;
        }

        $engine = new EngineInput($input);
        $shared['engine_input'] = $engine;

        return $engine;
    }

    /**
     * @return array{0: float, 1: float} [x, vx]
     */
    protected function bounceX(float $x, float $vx, float $minX, float $maxX, float $restitution): array
    {
        if ($x < $minX) {
            $x = $minX;
            $vx = abs($vx) * $restitution;
        } elseif ($x > $maxX) {
            $x = $maxX;
            $vx = -abs($vx) * $restitution;
        }

        return [$x, $vx];
    }

    /**
     * @return array{0: float, 1: float} [y, vy]
     */
    protected function bounceY(float $y, float $vy, float $minY, float $maxY, float $restitution): array
    {
        if ($y < $minY) {
            $y = $minY;
            $vy = abs($vy) * $restitution;
        } elseif ($y > $maxY) {
            $y = $maxY;
            $vy = -abs($vy) * $restitution;
        }

        return [$y, $vy];
    }
}
