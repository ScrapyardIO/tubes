<?php

use ScrapyardIO\Tubes\Fonts\Support\AdafruitGfxHeader;

test('AdafruitGfxHeader parses FreeSans9pt7b-shaped source', function () {
    $header = <<<'H'
const uint8_t FreeSans9pt7bBitmaps[] PROGMEM = {
    0xFF, 0xFF, 0xF8, 0xC0
};

const GFXglyph FreeSans9pt7bGlyphs[] PROGMEM = {
    {0, 0, 0, 5, 0, 1},        // 0x20 ' '
    {0, 2, 13, 6, 2, -12}     // 0x21 '!'
};

const GFXfont FreeSans9pt7b PROGMEM = {(uint8_t *)FreeSans9pt7bBitmaps,
                                       (GFXglyph *)FreeSans9pt7bGlyphs, 0x20,
                                       0x21, 22};
H;

    $parsed = AdafruitGfxHeader::parse($header);

    expect($parsed['first'])->toBe(0x20)
        ->and($parsed['last'])->toBe(0x21)
        ->and($parsed['yAdvance'])->toBe(22)
        ->and($parsed['bitmaps'])->toBe([0xFF, 0xFF, 0xF8, 0xC0])
        ->and($parsed['glyphs'])->toHaveCount(2)
        ->and($parsed['glyphs'][1][1])->toBe(2)
        ->and($parsed['glyphs'][1][5])->toBe(-12);
});

test('AdafruitGfxHeader renderClassSource emits a loadable GFXFont subclass shape', function () {
    $parsed = [
        'first' => 0x20,
        'last' => 0x20,
        'yAdvance' => 8,
        'bitmaps' => [0xAA],
        'glyphs' => [[0, 1, 1, 2, 0, 0, 'comment' => "0x20 ' '"]],
    ];

    $php = AdafruitGfxHeader::renderClassSource('App\\Fonts', 'TinyTestFont', $parsed);

    expect($php)->toContain('namespace App\\Fonts;')
        ->and($php)->toContain('class TinyTestFont extends GFXFont')
        ->and($php)->toContain('protected int $yAdvance = 8;')
        ->and($php)->toContain('0xAA');
});
