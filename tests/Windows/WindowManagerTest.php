<?php

use ScrapyardIO\Tubes\Canvas\OSWindow;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\BitDepth;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\Endianness;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\PixelFormat;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Framebuffers\DeferredFramebuffer;
use ScrapyardIO\Tubes\Windows\PendingWindow;
use ScrapyardIO\Tubes\Windows\WindowException;
use ScrapyardIO\Tubes\Windows\WindowHandler;
use ScrapyardIO\Tubes\Windows\WindowManager;

function managerFakeSpec(): FormatSpec
{
    return new FormatSpec(
        PixelFormat::ROW_MAJOR,
        BitDepth::B32,
        endianness: Endianness::MSB,
    );
}

function managerFakeHandler(string $title, int $width, int $height): WindowHandler
{
    return new class($title, $width, $height) extends WindowHandler
    {
        protected function defineFormatSpec(): FormatSpec
        {
            return managerFakeSpec();
        }

        protected function bootNative(): void {}

        protected function bindFramebuffer(): DeferredFramebuffer
        {
            return new class($this->width(), $this->height(), $this->formatSpec()) extends DeferredFramebuffer
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
                    return false;
                }
            };
        }

        protected function presentNative(): void {}

        protected function pollNative(): void {}

        public function shouldClose(): bool
        {
            return false;
        }

        protected function destroyNative(): void {}
    };
}

function registerFakeWindowDriver(WindowManager $manager, string $name = 'fake'): void
{
    $manager->extend(
        $name,
        fn (PendingWindow $pending): WindowHandler => managerFakeHandler(
            $pending->titleValue(),
            $pending->widthValue(),
            $pending->heightValue(),
        ),
    );
}

test('WindowManager extend and create returns OSWindow', function () {
    $manager = new WindowManager;
    registerFakeWindowDriver($manager);

    $window = $manager->driver('fake')
        ->title('Demo')
        ->size(640, 480)
        ->create();

    expect($window)->toBeInstanceOf(OSWindow::class)
        ->and($window->title())->toBe('Demo')
        ->and($window->width())->toBe(640)
        ->and($window->height())->toBe(480)
        ->and($manager->listWindows())->toContain('fake');
});

test('WindowManager make uses default from config', function () {
    $manager = new WindowManager([
        'default' => 'fake',
    ]);
    registerFakeWindowDriver($manager);

    expect($manager->defaultDriver())->toBe('fake');

    $window = $manager->make()->title('X')->size(8, 8)->create();

    expect($window)->toBeInstanceOf(OSWindow::class);
});

test('WindowManager driver without a name uses the default driver', function () {
    $manager = new WindowManager([
        'default' => 'fake',
    ]);
    registerFakeWindowDriver($manager);

    expect($manager->driver()->driver())->toBe('fake');
});

test('create fails fast when required extension is missing', function () {
    $manager = new WindowManager;
    registerFakeWindowDriver($manager, 'needs-ext');
    $manager->registerDriverDefinition('needs-ext', [
        'extension' => 'this_window_ext_does_not_exist',
    ]);

    expect(fn () => $manager->driver('needs-ext')->title('t')->size(4, 4)->create())
        ->toThrow(WindowException::class, 'PHP extension');
});

test('unknown window driver throws', function () {
    $manager = new WindowManager;

    expect(fn () => $manager->driver('missing'))
        ->toThrow(WindowException::class);
});

test('PendingWindow open creates and opens', function () {
    $manager = new WindowManager;
    registerFakeWindowDriver($manager);

    $window = $manager->driver('fake')->title('Live')->size(32, 24)->open();

    expect($window->isOpen())->toBeTrue();

    $window->close();
});
