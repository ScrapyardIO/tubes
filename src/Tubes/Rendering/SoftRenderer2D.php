<?php

namespace ScrapyardIO\Tubes\Rendering;

use ScrapyardIO\Tubes\Rendering\Concerns\DrawsText;
use ScrapyardIO\Tubes\Rendering\Renderer2D;

/**
 * Soft Renderer2D fallback for package sketches (no gfx companion) — forwards DrawingAPI calls to the
 * borrowed framebuffer. Not a microscrap gfx driver; companions override later.
 */
class SoftRenderer2D extends Renderer2D
{
    use DrawsText;
    public function drawPixel(int $x, int $y, int $color): static
    {
        $this->framebuffer()->setPixel($x, $y, $color);

        return $this;
    }

    public function drawPixels(array $pixels): static
    {
        $this->framebuffer()->setPixels($pixels);

        return $this;
    }

    public function drawSegment(int $x, int $y, int $width, int $height, int $color): static
    {
        $this->framebuffer()->setSegment($x, $y, $width, $height, $color);

        return $this;
    }

    public function drawHorizontalLine(int $x, int $y, int $w, int $color): static
    {
        return $this->drawSegment($x, $y, $w, 1, $color);
    }

    public function drawVerticalLine(int $x, int $y, int $h, int $color): static
    {
        return $this->drawSegment($x, $y, 1, $h, $color);
    }

    public function drawLine(int $x0, int $y0, int $x1, int $y1, int $color): static
    {
        if ($x0 === $x1) {
            $top = min($y0, $y1);

            return $this->drawVerticalLine($x0, $top, abs($y1 - $y0) + 1, $color);
        }

        if ($y0 === $y1) {
            $left = min($x0, $x1);

            return $this->drawHorizontalLine($left, $y0, abs($x1 - $x0) + 1, $color);
        }

        // Bresenham
        $steep = abs($y1 - $y0) > abs($x1 - $x0);
        if ($steep) {
            [$x0, $y0] = [$y0, $x0];
            [$x1, $y1] = [$y1, $x1];
        }
        if ($x0 > $x1) {
            [$x0, $x1] = [$x1, $x0];
            [$y0, $y1] = [$y1, $y0];
        }

        $dx = $x1 - $x0;
        $dy = abs($y1 - $y0);
        $err = intdiv($dx, 2);
        $yStep = $y0 < $y1 ? 1 : -1;
        $y = $y0;

        for ($x = $x0; $x <= $x1; $x++) {
            if ($steep) {
                $this->drawPixel($y, $x, $color);
            } else {
                $this->drawPixel($x, $y, $color);
            }
            $err -= $dy;
            if ($err < 0) {
                $y += $yStep;
                $err += $dx;
            }
        }

        return $this;
    }

    public function drawLines(array $lines): static
    {
        foreach ($lines as [$x0, $y0, $x1, $y1, $color]) {
            $this->drawLine($x0, $y0, $x1, $y1, $color);
        }

        return $this;
    }

    public function drawRect(int $x, int $y, int $w, int $h, int $color): static
    {
        $this->drawHorizontalLine($x, $y, $w, $color);
        $this->drawHorizontalLine($x, $y + $h - 1, $w, $color);
        $this->drawVerticalLine($x, $y, $h, $color);

        return $this->drawVerticalLine($x + $w - 1, $y, $h, $color);
    }

    public function fill(int $color): static
    {
        $this->framebuffer()->fill($color);

        return $this;
    }

    public function fillRect(int $x, int $y, int $w, int $h, int $color): static
    {
        return $this->drawSegment($x, $y, $w, $h, $color);
    }

    public function drawRoundRect(int $x, int $y, int $w, int $h, int $r, int $color): static
    {
        return $this->drawRect($x, $y, $w, $h, $color);
    }

    public function fillRoundRect(int $x, int $y, int $w, int $h, int $r, int $color): static
    {
        return $this->fillRect($x, $y, $w, $h, $color);
    }

    public function fillCircle(int $x0, int $y0, int $r, int $color): static
    {
        if ($r < 0) {
            return $this;
        }

        $this->drawVerticalLine($x0, $y0 - $r, 2 * $r + 1, $color);

        $f = 1 - $r;
        $ddF_x = 1;
        $ddF_y = -2 * $r;
        $x = 0;
        $y = $r;

        while ($x < $y) {
            if ($f >= 0) {
                $y--;
                $ddF_y += 2;
                $f += $ddF_y;
            }
            $x++;
            $ddF_x += 2;
            $f += $ddF_x;

            $this->drawVerticalLine($x0 + $x, $y0 - $y, 2 * $y + 1, $color);
            $this->drawVerticalLine($x0 - $x, $y0 - $y, 2 * $y + 1, $color);
            $this->drawVerticalLine($x0 + $y, $y0 - $x, 2 * $x + 1, $color);
            $this->drawVerticalLine($x0 - $y, $y0 - $x, 2 * $x + 1, $color);
        }

        return $this;
    }

    public function drawCircle(int $x0, int $y0, int $r, int $color): static
    {
        if ($r < 0) {
            return $this;
        }

        $f = 1 - $r;
        $ddF_x = 1;
        $ddF_y = -2 * $r;
        $x = 0;
        $y = $r;

        $this->drawPixel($x0, $y0 + $r, $color)
            ->drawPixel($x0, $y0 - $r, $color)
            ->drawPixel($x0 + $r, $y0, $color)
            ->drawPixel($x0 - $r, $y0, $color);

        while ($x < $y) {
            if ($f >= 0) {
                $y--;
                $ddF_y += 2;
                $f += $ddF_y;
            }
            $x++;
            $ddF_x += 2;
            $f += $ddF_x;

            $this->drawPixel($x0 + $x, $y0 + $y, $color)
                ->drawPixel($x0 - $x, $y0 + $y, $color)
                ->drawPixel($x0 + $x, $y0 - $y, $color)
                ->drawPixel($x0 - $x, $y0 - $y, $color)
                ->drawPixel($x0 + $y, $y0 + $x, $color)
                ->drawPixel($x0 - $y, $y0 + $x, $color)
                ->drawPixel($x0 + $y, $y0 - $x, $color)
                ->drawPixel($x0 - $y, $y0 - $x, $color);
        }

        return $this;
    }

    public function drawEllipse(int $x0, int $y0, int $rw, int $rh, int $color): static
    {
        return $this->drawRect($x0 - $rw, $y0 - $rh, $rw * 2 + 1, $rh * 2 + 1, $color);
    }

    public function fillEllipse(int $x0, int $y0, int $rw, int $rh, int $color): static
    {
        return $this->fillRect($x0 - $rw, $y0 - $rh, $rw * 2 + 1, $rh * 2 + 1, $color);
    }

    public function drawTriangle(int $x0, int $y0, int $x1, int $y1, int $x2, int $y2, int $color): static
    {
        return $this->drawLine($x0, $y0, $x1, $y1, $color)
            ->drawLine($x1, $y1, $x2, $y2, $color)
            ->drawLine($x2, $y2, $x0, $y0, $color);
    }

    public function fillTriangle(int $x0, int $y0, int $x1, int $y1, int $x2, int $y2, int $color): static
    {
        return $this->drawTriangle($x0, $y0, $x1, $y1, $x2, $y2, $color);
    }
}
