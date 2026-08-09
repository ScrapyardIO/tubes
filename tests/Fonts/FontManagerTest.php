<?php

use ScrapyardIO\Tubes\Contracts\Fonts\GFXFont;
use ScrapyardIO\Tubes\Fonts\ClassicFont;
use ScrapyardIO\Tubes\Fonts\FontManager;

test('FontManager registers classic and companions via extend', function () {
    $manager = new FontManager;

    expect($manager->hasFont('classic'))->toBeTrue()
        ->and($manager->font('classic'))->toBeInstanceOf(ClassicFont::class)
        ->and($manager->font('classic'))->toBeInstanceOf(GFXFont::class);

    $companion = new class extends GFXFont
    {
        protected int $first = 65;

        protected int $last = 65;

        protected int $yAdvance = 8;
    };

    $manager->extend('stub', $companion::class);

    expect($manager->hasFont('stub'))->toBeTrue()
        ->and($manager->listFonts())->toHaveKey('classic')
        ->and($manager->listFonts())->toHaveKey('stub');
});

test('FontManager addFont is an alias of extend', function () {
    $manager = new FontManager;
    $stub = new class extends GFXFont {};
    $manager->addFont('extra', $stub::class);

    expect($manager->hasFont('extra'))->toBeTrue();
});

test('FontManager driver and font without a name use the default slug', function () {
    $manager = new FontManager([
        'default' => 'classic',
    ]);

    expect($manager->defaultFont())->toBe('classic')
        ->and($manager->defaultDriver())->toBe('classic')
        ->and($manager->font())->toBeInstanceOf(ClassicFont::class)
        ->and($manager->driver())->toBe($manager->font('classic'));
});
