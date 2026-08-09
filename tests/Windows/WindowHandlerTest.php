<?php

use ScrapyardIO\Tubes\Canvas\OSWindow;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\BitDepth;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\Endianness;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\PixelFormat;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Framebuffers\DeferredFramebuffer;
use ScrapyardIO\Tubes\Windows\WindowException;
use ScrapyardIO\Tubes\Windows\WindowHandler;

function fakeWindowSpec(): FormatSpec
{
    return new FormatSpec(
        PixelFormat::ROW_MAJOR,
        BitDepth::B32,
        endianness: Endianness::MSB,
    );
}

function fakeWindowHandler(string $title = 'demo', int $w = 320, int $h = 240): WindowHandler
{
    return new class($title, $w, $h) extends WindowHandler
    {
        public int $presents = 0;

        public int $polls = 0;

        public bool $booted = false;

        public bool $destroyed = false;

        protected function defineFormatSpec(): FormatSpec
        {
            return fakeWindowSpec();
        }

        protected function bootNative(): void
        {
            $this->booted = true;
        }

        protected function bindFramebuffer(): DeferredFramebuffer
        {
            $spec = $this->formatSpec();
            $width = $this->width();
            $height = $this->height();

            return new class($width, $height, $spec) extends DeferredFramebuffer
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

        protected function presentNative(): void
        {
            $this->presents++;
            $this->framebuffer()->present();
        }

        protected function pollNative(): void
        {
            $this->polls++;
        }

        public function shouldClose(): bool
        {
            return false;
        }

        protected function destroyNative(): void
        {
            $this->destroyed = true;
        }
    };
}

test('WindowHandler defines FormatSpec at construct without a framebuffer', function () {
    $handler = fakeWindowHandler();

    expect($handler->formatSpec()->bit_depth)->toBe(BitDepth::B32)
        ->and($handler->isOpen())->toBeFalse()
        ->and(fn () => $handler->framebuffer())->toThrow(WindowException::class);
});

test('open binds deferred framebuffer; present bypasses flush', function () {
    $handler = fakeWindowHandler('win', 64, 48);
    $handler->open();

    expect($handler->isOpen())->toBeTrue()
        ->and($handler->booted)->toBeTrue()
        ->and($handler->framebuffer())->toBeInstanceOf(DeferredFramebuffer::class)
        ->and($handler->framebuffer()->isHeadless())->toBeFalse();

    $handler->present()->pollEvents();

    expect($handler->presents)->toBe(1)
        ->and($handler->polls)->toBe(1);

    $handler->close();

    expect($handler->isOpen())->toBeFalse()
        ->and($handler->destroyed)->toBeTrue()
        ->and(fn () => $handler->present())->toThrow(WindowException::class);
});

test('OSWindow wraps WindowHandler for canvas present', function () {
    $handler = fakeWindowHandler('canvas', 800, 600);
    $window = new OSWindow($handler);

    expect($window->title())->toBe('canvas')
        ->and($window->width())->toBe(800)
        ->and($window->height())->toBe(600)
        ->and($window->formatSpec()->pixel_format)->toBe(PixelFormat::ROW_MAJOR);

    $window->open();

    $fb = $window->framebuffer();
    $fb->setPixel(1, 1, 0xFFFFFFFF);

    $window->present()->pollEvents();

    expect($window->shouldClose())->toBeFalse()
        ->and($window->deferredFramebuffer())->toBe($fb)
        ->and($handler->presents)->toBe(1);

    $window->close();
});
