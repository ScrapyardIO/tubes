<?php

use ScrapyardIO\Tubes\Contracts\Framebuffers\DeferredFramebuffer;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\BitDepth;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\Endianness;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\FramebufferDriver;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\FramebufferKind;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\PixelFormat;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Framebuffer;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FramebufferException;
use ScrapyardIO\Tubes\Framebuffers\DirtyRegionsBuffer;
use ScrapyardIO\Tubes\Framebuffers\FramebufferManager;
use ScrapyardIO\Tubes\Framebuffers\FullFramebuffer;
use ScrapyardIO\Tubes\Framebuffers\PageSegmentBuffer;
use ScrapyardIO\Tubes\Framebuffers\PendingFramebuffer;

function rowMajorRgb565(): FormatSpec
{
    return new FormatSpec(
        PixelFormat::ROW_MAJOR,
        BitDepth::B16,
        endianness: Endianness::MSB,
    );
}

function monoVerticalPage(): FormatSpec
{
    return new FormatSpec(
        PixelFormat::MONO_VERTICAL_PAGE,
        BitDepth::B1,
        bit_order: \ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\BitOrder::LSB_FIRST,
    );
}

test('fluent full managed framebuffer builds via pending', function () {
    $manager = new FramebufferManager;

    $buffer = $manager->full()
        ->size(16, 8)
        ->format(rowMajorRgb565())
        ->layers(1)
        ->create();

    expect($buffer)->toBeInstanceOf(FullFramebuffer::class)
        ->and($buffer->viewportWidth())->toBe(16)
        ->and($buffer->viewportHeight())->toBe(8);
});

test('driver enum and string resolve the same built-in', function () {
    $manager = new FramebufferManager;
    $spec = rowMajorRgb565();

    $viaEnum = $manager->driver(FramebufferDriver::DIRTY)->size(8, 8)->format($spec)->create();
    $viaString = $manager->driver('dirty')->size(8, 8)->format($spec)->create();

    expect($viaEnum)->toBeInstanceOf(DirtyRegionsBuffer::class)
        ->and($viaString)->toBeInstanceOf(DirtyRegionsBuffer::class);
});

test('page strategy requires mono vertical page host', function () {
    $manager = new FramebufferManager;

    $buffer = $manager->page()
        ->size(128, 64)
        ->hostFormat(monoVerticalPage())
        ->create();

    expect($buffer)->toBeInstanceOf(PageSegmentBuffer::class);
});

test('extendManaged registers class-string and callable', function () {
    $manager = new FramebufferManager;

    $manager->extendManaged('custom-full', FullFramebuffer::class);

    $fromClass = $manager->managed('custom-full')
        ->size(4, 4)
        ->format(rowMajorRgb565())
        ->get();

    $manager->extendManaged('callable-full', function (PendingFramebuffer $pending): Framebuffer {
        return FullFramebuffer::sized(
            $pending->widthValue(),
            $pending->heightValue(),
            $pending->hostFormatValue(),
            $pending->layersValue(),
        );
    });

    $fromCallable = $manager->managed('callable-full')
        ->size(4, 4)
        ->format(rowMajorRgb565())
        ->create();

    expect($fromClass)->toBeInstanceOf(FullFramebuffer::class)
        ->and($fromCallable)->toBeInstanceOf(FullFramebuffer::class)
        ->and($manager->kindOf('custom-full'))->toBe(FramebufferKind::MANAGED);
});

test('cannot register managed name over deferred', function () {
    $manager = new FramebufferManager;

    $manager->extendDeferred('gl-only', function (PendingFramebuffer $pending): Framebuffer {
        return FullFramebuffer::sized(
            $pending->widthValue(),
            $pending->heightValue(),
            $pending->hostFormatValue(),
        );
    });

    expect(fn () => $manager->extendManaged('gl-only', FullFramebuffer::class))
        ->toThrow(FramebufferException::class);
});

test('extendDeferred registers external deferred implementations', function () {
    $manager = new FramebufferManager;

    $manager->extendDeferred('fake-gl', function (PendingFramebuffer $pending): Framebuffer {
        return new class ($pending->widthValue(), $pending->heightValue(), $pending->hostFormatValue()) extends \ScrapyardIO\Tubes\Framebuffers\DeferredFramebuffer
        {
            public function getPixel(int $x, int $y): int
            {
                return 0;
            }

            public function setPixel(int $x, int $y, int $value): static
            {
                return $this;
            }

            public function setSegment(int $x, int $y, int $width, int $height, int $color): static
            {
                return $this;
            }

            public function dump(?int $layer = null): string
            {
                return '';
            }

            public function flush(FormatSpec $spec, bool $as_array = false): string|array
            {
                return '';
            }

            public function present(): static
            {
                return $this;
            }

            public function isHeadless(): bool
            {
                return true;
            }
        };
    });

    $buffer = $manager->deferred('fake-gl')
        ->size(320, 240)
        ->format(rowMajorRgb565())
        ->option('window', 'main')
        ->create();

    expect($buffer)->toBeInstanceOf(DeferredFramebuffer::class)
        ->and($buffer->viewportWidth())->toBe(320)
        ->and($manager->kindOf('fake-gl'))->toBe(FramebufferKind::DEFERRED)
        ->and($manager->listFramebuffers(FramebufferKind::DEFERRED))->toContain('fake-gl');
});

test('make uses default driver from config', function () {
    $manager = new FramebufferManager([
        'default' => 'dirty',
        'drivers' => [],
    ]);

    expect($manager->defaultDriver())->toBe('dirty');

    $buffer = $manager->make()
        ->size(8, 8)
        ->format(rowMajorRgb565())
        ->create();

    expect($buffer)->toBeInstanceOf(DirtyRegionsBuffer::class);
});

test('config directory registers deferred slug definitions', function () {
    $dir = sys_get_temp_dir().'/tubes-fb-config-'.uniqid();
    mkdir($dir);
    file_put_contents($dir.'/custom.php', <<<'PHP'
<?php
return [
    'kind' => 'managed',
    'class' => ScrapyardIO\Tubes\Framebuffers\FullFramebuffer::class,
];
PHP);

    $manager = new FramebufferManager;
    $manager->registerFromConfigDirectory($dir);

    $buffer = $manager->driver('custom')->size(4, 4)->format(rowMajorRgb565())->create();

    expect($buffer)->toBeInstanceOf(FullFramebuffer::class);

    array_map('unlink', glob($dir.'/*') ?: []);
    rmdir($dir);
});

test('create fails fast when required extension is missing', function () {
    $manager = new FramebufferManager;
    $manager->registerDriverDefinition('needs-ext', [
        'kind' => 'managed',
        'class' => FullFramebuffer::class,
        'extension' => 'this_extension_does_not_exist_tubes_test',
    ]);

    expect(fn () => $manager->driver('needs-ext')->size(4, 4)->format(rowMajorRgb565())->create())
        ->toThrow(FramebufferException::class, 'PHP extension');
});

test('create requires size and host format', function () {
    $manager = new FramebufferManager;

    expect(fn () => $manager->full()->create())
        ->toThrow(FramebufferException::class);

    expect(fn () => $manager->full()->size(8, 8)->create())
        ->toThrow(FramebufferException::class);
});

test('cannot register deferred name over managed built-in', function () {
    $manager = new FramebufferManager;

    expect(fn () => $manager->extendDeferred('full', fn () => null))
        ->toThrow(FramebufferException::class);
});

test('unknown driver throws', function () {
    $manager = new FramebufferManager;

    expect(fn () => $manager->driver('missing'))
        ->toThrow(FramebufferException::class);
});
