<?php

namespace ScrapyardIO\Tubes\Matrix;

use Closure;
use Fabricate\Contracts\Displays\DisplayException;
use Fabricate\Contracts\Displays\Interfaces\LEDMatrixDisplay;
use Fabricate\Displays\EmbeddedDisplay;
use Fabricate\Framebuffers\DataObjects\DumpedBuffer;
use Fabricate\NutsAndBolts\MagicAliases\Circuit;
use ScrapyardIO\Tubes\Matrix\Enums\MatrixWiring;

class RGBMatrix extends EmbeddedDisplay
{
    /** @var Closure(int, int, int, int): int|array<int, int>|null */
    protected Closure|array|null $mapping = null;

    public function __construct(
        LEDMatrixDisplay $circuit,
        protected MatrixWiring $matrix_wiring = MatrixWiring::SERPENTINE,
    ) {
        parent::__construct($circuit);
    }

    public static function circuit(string $driver): static
    {
        $circuit = Circuit::driver($driver);

        if ($circuit instanceof LEDMatrixDisplay) {
            return new static($circuit);
        }

        $circuit->close();
        throw new DisplayException("Circuit [{$driver}] is not an LED Matrix DisplayPanel.");
    }

    public function wiring(?MatrixWiring $wiring = null): MatrixWiring|static
    {
        if (is_null($wiring)) {
            return $this->matrix_wiring;
        }

        $this->matrix_wiring = $wiring;

        return $this;
    }

    /**
     * Override the XY-to-linear mapping with either a callable or an index map.
     *
     * @param callable(int, int, int, int): int|array<int, int> $mapping
     */
    public function map(callable|array $mapping): static
    {
        $this->mapping = is_array($mapping)
            ? $mapping
            : Closure::fromCallable($mapping);

        return $this;
    }

    public function flush(DumpedBuffer $frame): void
    {
        $width = $frame->width ?? $this->width();
        $height = $frame->height ?? $this->height();
        $pixels = $this->flatten($frame->raw_data);
        $mapped = array_fill(0, $width * $height, 0);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $source = ($y * $width) + $x;
                $target = $this->indexAt($x, $y, $width, $height);
                $mapped[$target] = $pixels[$source] ?? 0;
            }
        }

        $this->panel->transmit(new DumpedBuffer(
            render_type: $frame->render_type,
            metadata: $frame->metadata,
            raw_data: $mapped,
            origin_x: $frame->origin_x,
            origin_y: $frame->origin_y,
            width: $width,
            height: $height,
        ));
    }

    protected function indexAt(int $x, int $y, int $width, int $height): int
    {
        if ($this->mapping instanceof Closure) {
            return ($this->mapping)($x, $y, $width, $height);
        }

        $source = ($y * $width) + $x;
        if (is_array($this->mapping)) {
            return $this->mapping[$source] ?? $source;
        }

        if ($this->matrix_wiring === MatrixWiring::SERPENTINE && ($y % 2) === 1) {
            return ($y * $width) + ($width - 1 - $x);
        }

        return $source;
    }

    /**
     * @param array<int, int>|array<int, array<int, int>> $data
     * @return list<int>
     */
    protected function flatten(array $data): array
    {
        $pixels = [];
        array_walk_recursive($data, static function (int $pixel) use (&$pixels): void {
            $pixels[] = $pixel;
        });

        return $pixels;
    }
}
