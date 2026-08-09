<?php

use ScrapyardIO\Tubes\Core\Runner\Sketches\Support\MetalCanvasHud;

test('MetalCanvasHud maps speed to warmer accents', function () {
    $slow = MetalCanvasHud::accentForSpeed(0.0);
    $fast = MetalCanvasHud::accentForSpeed(12.0);

    expect($slow)->toBe(0x4080F0FF)
        ->and($fast)->toBe(0xF0A030FF)
        ->and(($fast >> 24) & 0xFF)->toBeGreaterThan(($slow >> 24) & 0xFF);
});

test('MetalCanvasHud blends measured fps from frame deltas', function () {
    $fps = MetalCanvasHud::blendFps(0.0, 16_666_667);
    expect($fps)->toBeGreaterThan(55.0)->toBeLessThan(65.0);

    $smoothed = MetalCanvasHud::blendFps($fps, 33_333_333);
    expect($smoothed)->toBeLessThan($fps);
});

test('MetalCanvasHud lines include velocity accel fps and color', function () {
    $lines = MetalCanvasHud::lines(7.1, -2.4, 0.5, -0.1, 59.2, 0xF0A030FF, 60);

    expect($lines)->toHaveCount(4)
        ->and($lines[0])->toContain('7.10')
        ->and($lines[1])->toContain('0.50')
        ->and($lines[2])->toContain('59.2')
        ->and($lines[3])->toContain('#F0A030FF');
});

test('MetalCanvasHud lines append boost remaining when active', function () {
    $lines = MetalCanvasHud::lines(7.1, -2.4, 0.5, -0.1, 59.2, 0xF0A030FF, 60, 2.5);

    expect($lines)->toHaveCount(5)
        ->and($lines[4])->toContain('boost')
        ->and($lines[4])->toContain('2.5');
});

test('MetalCanvasHud font slug resolver prefers helvb when present', function () {
    $available = ['helvb-12' => true, 'logisoso-16' => true, 'free-sans-9pt' => true];
    $slugs = MetalCanvasHud::resolveFontSlugs(fn (string $s): bool => $available[$s] ?? false);

    expect($slugs['label'])->toBe('helvb-12')
        ->and($slugs['value'])->toBe('helvb-12');
});

test('MetalCanvasHud baseline clears custom-font ascent', function () {
    $font = new class
    {
        public function getCapHeight(): int
        {
            return 16;
        }
    };

    expect(MetalCanvasHud::hudBaselineY(null))->toBe(8)
        ->and(MetalCanvasHud::hudBaselineY($font))->toBe(20);
});
