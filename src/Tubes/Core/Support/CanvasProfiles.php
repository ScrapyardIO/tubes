<?php

namespace ScrapyardIO\Tubes\Core\Support;

use InvalidArgumentException;
use ScrapyardIO\Tubes\Core\Enums\CanvasProfileKind;

/**
 * Resolve canvas profile arrays from config('tubes.canvas_profiles.*').
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
