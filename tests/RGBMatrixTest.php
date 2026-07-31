<?php

use Fabricate\Contracts\Displays\Interfaces\LEDMatrixDisplay;
use Fabricate\Contracts\Framebuffers\Enums\BitDepth;
use Fabricate\Contracts\Framebuffers\Enums\PixelFormat;
use Fabricate\Contracts\Framebuffers\Enums\RenderType;
use Fabricate\Framebuffers\DataObjects\DumpedBuffer;
use Fabricate\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Matrix\Enums\MatrixWiring;
use ScrapyardIO\Tubes\Matrix\RGBMatrix;

it('defaults to serpentine wiring and remaps rows', function () {
    $panel = new class implements LEDMatrixDisplay {
        public ?DumpedBuffer $frame = null;

        public function width(): int
        {
            return 3;
        }

        public function height(): int
        {
            return 2;
        }

        public function formatSpec(): FormatSpec
        {
            return new FormatSpec(PixelFormat::ROW_MAJOR, BitDepth::B24);
        }

        public function transmit(DumpedBuffer $frame): void
        {
            $this->frame = $frame;
        }

        public function close(): void {}
    };

    $matrix = new RGBMatrix($panel);
    $matrix->flush(new DumpedBuffer(
        RenderType::FULL,
        $panel->formatSpec(),
        [1, 2, 3, 4, 5, 6],
        width: 3,
        height: 2,
    ));

    expect($matrix->wiring())->toBe(MatrixWiring::SERPENTINE)
        ->and($panel->frame?->raw_data)->toBe([1, 2, 3, 6, 5, 4]);
});

it('supports progressive and custom mappings', function () {
    $panel = new class implements LEDMatrixDisplay {
        public ?DumpedBuffer $frame = null;

        public function width(): int
        {
            return 2;
        }

        public function height(): int
        {
            return 2;
        }

        public function formatSpec(): FormatSpec
        {
            return new FormatSpec(PixelFormat::ROW_MAJOR, BitDepth::B24);
        }

        public function transmit(DumpedBuffer $frame): void
        {
            $this->frame = $frame;
        }

        public function close(): void {}
    };

    $matrix = new RGBMatrix($panel);
    $matrix->wiring(MatrixWiring::PROGRESSIVE)->flush(new DumpedBuffer(
        RenderType::FULL,
        $panel->formatSpec(),
        [1, 2, 3, 4],
        width: 2,
        height: 2,
    ));
    expect($panel->frame?->raw_data)->toBe([1, 2, 3, 4]);

    $matrix->map([0 => 3, 1 => 2, 2 => 1, 3 => 0])->flush(new DumpedBuffer(
        RenderType::FULL,
        $panel->formatSpec(),
        [1, 2, 3, 4],
        width: 2,
        height: 2,
    ));
    expect($panel->frame?->raw_data)->toBe([4, 3, 2, 1]);
});
