<?php

use ScrapyardIO\Tubes\Core\Enums\CanvasProfileKind;
use ScrapyardIO\Tubes\Core\Support\CanvasProfiles;

test('CanvasProfiles resolves short slugs and dotted config paths', function () {
    expect(CanvasProfiles::configPath('demo', CanvasProfileKind::WINDOWS))
        ->toBe('tubes.canvas_profiles.windows.demo')
        ->and(CanvasProfiles::configPath('tubes.canvas_profiles.windows.demo', CanvasProfileKind::WINDOWS))
        ->toBe('tubes.canvas_profiles.windows.demo')
        ->and(CanvasProfiles::configPath('canvas_profiles.windows.demo', CanvasProfileKind::WINDOWS))
        ->toBe('tubes.canvas_profiles.windows.demo')
        ->and(CanvasProfiles::configPath('windows.demo', CanvasProfileKind::WINDOWS))
        ->toBe('tubes.canvas_profiles.windows.demo')
        ->and(CanvasProfiles::configPath('oled', CanvasProfileKind::PANELS))
        ->toBe('tubes.canvas_profiles.panels.oled');
});

test('CanvasProfiles rejects dotted paths that do not match the requested kind', function () {
    expect(fn () => CanvasProfiles::configPath(
        'tubes.canvas_profiles.panels.oled',
        CanvasProfileKind::WINDOWS,
    ))->toThrow(InvalidArgumentException::class);
});
