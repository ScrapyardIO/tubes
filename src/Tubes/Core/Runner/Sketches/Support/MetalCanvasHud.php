<?php

namespace ScrapyardIO\Tubes\Core\Runner\Sketches\Support;

/**
 * Pure helpers for MetalCanvas HUD (speed→color, fps EMA, text lines).
 */
final class MetalCanvasHud
{
    /**
     * Map speed (px/s) to an RGBA accent — cool blue → hot amber.
     *
     * Default max (~720) matches the old 12 px/frame feel at 60fps.
     */
    public static function accentForSpeed(float $speed, float $maxSpeed = 720.0): int
    {
        $t = $maxSpeed > 0.0 ? max(0.0, min(1.0, $speed / $maxSpeed)) : 0.0;

        $r = (int) round(0x40 + $t * (0xF0 - 0x40));
        $g = (int) round(0x80 + $t * (0xA0 - 0x80));
        $b = (int) round(0xF0 + $t * (0x30 - 0xF0));

        return ($r << 24) | ($g << 16) | ($b << 8) | 0xFF;
    }

    public static function speed(float $vx, float $vy): float
    {
        return hypot($vx, $vy);
    }

    /**
     * Exponential moving average of instantaneous fps from frame Δt (ns).
     */
    public static function blendFps(float $previous, int $deltaNs, float $alpha = 0.15): float
    {
        if ($deltaNs <= 0) {
            return $previous > 0.0 ? $previous : 0.0;
        }

        $instant = 1_000_000_000.0 / $deltaNs;
        if ($previous <= 0.0) {
            return $instant;
        }

        return ($alpha * $instant) + ((1.0 - $alpha) * $previous);
    }

    /**
     * @return list<string>
     */
    public static function lines(
        float $vx,
        float $vy,
        float $ax,
        float $ay,
        float $fps,
        int $accent,
        int $targetFps,
        float $boostRemaining = 0.0,
    ): array {
        $lines = [
            sprintf('v  %+6.2f %+6.2f', $vx, $vy),
            sprintf('a  %+6.2f %+6.2f', $ax, $ay),
            sprintf('fps %5.1f / %d', $fps, $targetFps),
            sprintf('c  #%08X', $accent),
        ];

        if ($boostRemaining > 0.0) {
            $lines[] = sprintf('boost %4.1fs', $boostRemaining);
        }

        return $lines;
    }

    /**
     * Prefer helvb for the HUD face (compact); logisoso is tall and clips easily.
     * Fall back to free-sans / classic.
     *
     * @return array{label: string|null, value: string|null}
     */
    public static function resolveFontSlugs(callable $hasFont): array
    {
        $label = null;
        foreach (['helvb-12', 'free-sans-9pt'] as $slug) {
            if ($hasFont($slug)) {
                $label = $slug;
                break;
            }
        }

        $value = null;
        foreach (['helvb-12', 'free-sans-9pt', 'logisoso-16'] as $slug) {
            if ($hasFont($slug)) {
                $value = $slug;
                break;
            }
        }

        return ['label' => $label, 'value' => $value];
    }

    /**
     * Baseline Y so Adafruit custom glyphs (negative yOffset) do not clip the top edge.
     */
    public static function hudBaselineY(?object $font, int $padding = 4, int $classicY = 8): int
    {
        if (is_null($font) || ! method_exists($font, 'getCapHeight')) {
            return $classicY;
        }

        $ascent = (int) $font->getCapHeight();

        return max($classicY, $ascent + $padding);
    }
}
