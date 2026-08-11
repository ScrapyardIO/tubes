<?php

namespace ScrapyardIO\Tubes\Core\Support;

use InvalidArgumentException;
use ScrapyardIO\Tubes\Core\Enums\CanvasProfileKind;

/**
 * Resolve canvas profile arrays from config('tubes.canvas_profiles.*').
 *
 * {@see tubes.defaults.canvas} may name any profile under windows or panels.
 */
final class CanvasProfiles
{
    /**
     * @return array<string, mixed>
     */
    public static function window(string $name): array
    {
        return self::resolve($name, CanvasProfileKind::WINDOWS);
    }

    /**
     * @return array<string, mixed>
     */
    public static function panel(string $name): array
    {
        return self::resolve($name, CanvasProfileKind::PANELS);
    }

    /**
     * Locate a slug under windows or panels (used by tubes.defaults.canvas).
     *
     * Ambiguous names that exist in both segments must use a dotted path
     * (`windows.demo` / `panels.st7796-front`).
     *
     * @return array{0: CanvasProfileKind, 1: array<string, mixed>}
     */
    public static function locate(string $name): array
    {
        $trimmed = trim($name);

        if ($trimmed === '') {
            throw new InvalidArgumentException('Canvas profile name must be non-empty.');
        }

        if (str_starts_with($trimmed, 'tubes.canvas_profiles.windows.')
            || str_starts_with($trimmed, 'canvas_profiles.windows.')
            || str_starts_with($trimmed, 'windows.')
        ) {
            return [CanvasProfileKind::WINDOWS, self::window($trimmed)];
        }

        if (str_starts_with($trimmed, 'tubes.canvas_profiles.panels.')
            || str_starts_with($trimmed, 'canvas_profiles.panels.')
            || str_starts_with($trimmed, 'panels.')
        ) {
            return [CanvasProfileKind::PANELS, self::panel($trimmed)];
        }

        $inWindows = self::defined($trimmed, CanvasProfileKind::WINDOWS);
        $inPanels = self::defined($trimmed, CanvasProfileKind::PANELS);

        if ($inWindows && $inPanels) {
            throw new InvalidArgumentException(
                "Canvas profile [{$trimmed}] exists under both windows and panels. "
                .'Disambiguate with windows.'.$trimmed.' or panels.'.$trimmed.'.'
            );
        }

        if ($inPanels) {
            return [CanvasProfileKind::PANELS, self::panel($trimmed)];
        }

        if ($inWindows) {
            return [CanvasProfileKind::WINDOWS, self::window($trimmed)];
        }

        throw new InvalidArgumentException(
            "Canvas profile [{$trimmed}] is not defined under tubes.canvas_profiles.windows or .panels."
        );
    }

    public static function kindOf(string $name): CanvasProfileKind
    {
        return self::locate($name)[0];
    }

    public static function defined(string $name, CanvasProfileKind $kind): bool
    {
        try {
            self::resolve($name, $kind);

            return true;
        } catch (InvalidArgumentException) {
            return false;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function resolve(string $name, CanvasProfileKind $kind): array
    {
        $trimmed = trim($name);

        if ($trimmed === '') {
            throw new InvalidArgumentException('Canvas profile name must be non-empty.');
        }

        $path = self::configPath($trimmed, $kind);
        $definition = function_exists('config') ? config($path) : null;

        if (! is_array($definition) || $definition === []) {
            throw new InvalidArgumentException(
                "Canvas {$kind->value} profile [{$trimmed}] is not defined at config('{$path}')."
            );
        }

        return $definition;
    }

    public static function configPath(string $name, CanvasProfileKind $kind): string
    {
        $trimmed = trim($name);

        if (str_starts_with($trimmed, 'tubes.canvas_profiles.')) {
            self::assertPathMatchesKind($trimmed, $kind);

            return $trimmed;
        }

        if (str_starts_with($trimmed, 'canvas_profiles.')) {
            $path = 'tubes.'.$trimmed;
            self::assertPathMatchesKind($path, $kind);

            return $path;
        }

        $prefix = 'tubes.canvas_profiles.'.$kind->value.'.';

        if (str_starts_with($trimmed, $kind->value.'.')) {
            return 'tubes.canvas_profiles.'.$trimmed;
        }

        return $prefix.$trimmed;
    }

    protected static function assertPathMatchesKind(string $path, CanvasProfileKind $kind): void
    {
        $segment = 'tubes.canvas_profiles.'.$kind->value.'.';

        if (! str_starts_with($path, $segment)) {
            throw new InvalidArgumentException(
                "Canvas profile path [{$path}] is not under {$segment}."
            );
        }
    }
}
