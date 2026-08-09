<?php

namespace ScrapyardIO\Tubes\Core\Runner\Sketches\Support;

/**
 * Pure helpers: hit-test the ball and apply a short click acceleration boost.
 */
final class MetalCanvasClickBoost
{
    /**
     * Boost window length in seconds.
     */
    public static function durationSeconds(): float
    {
        return 3.0;
    }

    /**
     * Barely-significant per-frame Δv while boost is active (px/frame² scale).
     */
    public static function accelPerFrame(): float
    {
        return 0.06;
    }

    /**
     * True when (mx, my) is inside the ball circle.
     *
     * @param  array{x?: float|int, y?: float|int, r?: float|int}  $ball
     */
    public static function hitsBall(float $mx, float $my, array $ball): bool
    {
        $x = (float) ($ball['x'] ?? 0.0);
        $y = (float) ($ball['y'] ?? 0.0);
        $r = max(1.0, (float) ($ball['r'] ?? 1.0));

        return hypot($mx - $x, $my - $y) <= $r;
    }

    public static function boostUntilFromNow(?float $now = null): float
    {
        $now ??= microtime(true);

        return $now + self::durationSeconds();
    }

    public static function remainingSeconds(float $boostUntil, ?float $now = null): float
    {
        $now ??= microtime(true);

        return max(0.0, $boostUntil - $now);
    }

    public static function isActive(float $boostUntil, ?float $now = null): bool
    {
        return self::remainingSeconds($boostUntil, $now) > 0.0;
    }

    /**
     * Acceleration to add this frame along the current velocity (or last facing).
     *
     * @return array{0: float, 1: float} [ax, ay]
     */
    public static function frameAcceleration(float $vx, float $vy, float $facingX = 1.0, float $facingY = 0.0): array
    {
        $speed = hypot($vx, $vy);
        if ($speed > 0.05) {
            $ux = $vx / $speed;
            $uy = $vy / $speed;
        } else {
            $face = hypot($facingX, $facingY);
            if ($face <= 0.0) {
                return [self::accelPerFrame(), 0.0];
            }
            $ux = $facingX / $face;
            $uy = $facingY / $face;
        }

        $a = self::accelPerFrame();

        return [$ux * $a, $uy * $a];
    }
}
