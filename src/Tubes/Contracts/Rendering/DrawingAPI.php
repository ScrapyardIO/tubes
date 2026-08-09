<?php

namespace ScrapyardIO\Tubes\Contracts\Rendering;

use ScrapyardIO\Tubes\Contracts\Fonts\GFXFont;

/**
 * Shared 2D draw surface for tubes {@see \ScrapyardIO\Tubes\Rendering\Renderer2D}
 * and every microscrap `*-gfx` implementation.
 *
 * Scalars / plain arrays for geometry; {@see GFXFont} (or registered name) for setFont.
 * Callers bind a framebuffer on the renderer (set/unset); draw methods write
 * into that borrowed buffer.
 */
interface DrawingAPI
{
    public function drawPixel(int $x, int $y, int $color): static;

    /**
     * @param  array<int, array{0: int, 1: int, 2: int}>  $pixels  Each entry is [x, y, color]
     */
    public function drawPixels(array $pixels): static;

    public function drawSegment(int $x, int $y, int $width, int $height, int $color): static;

    public function drawLine(int $x0, int $y0, int $x1, int $y1, int $color): static;

    public function drawHorizontalLine(int $x, int $y, int $w, int $color): static;

    public function drawVerticalLine(int $x, int $y, int $h, int $color): static;

    /**
     * @param  array<int, array{0: int, 1: int, 2: int, 3: int, 4: int}>  $lines  Each entry is [x0, y0, x1, y1, color]
     */
    public function drawLines(array $lines): static;

    public function drawRect(int $x, int $y, int $w, int $h, int $color): static;

    public function drawRoundRect(int $x, int $y, int $w, int $h, int $r, int $color): static;

    public function fill(int $color): static;

    public function fillRect(int $x, int $y, int $w, int $h, int $color): static;

    public function fillRoundRect(int $x, int $y, int $w, int $h, int $r, int $color): static;

    public function fillCircle(int $x0, int $y0, int $r, int $color): static;

    public function drawCircle(int $x0, int $y0, int $r, int $color): static;

    public function drawEllipse(int $x0, int $y0, int $rw, int $rh, int $color): static;

    public function fillEllipse(int $x0, int $y0, int $rw, int $rh, int $color): static;

    public function drawTriangle(int $x0, int $y0, int $x1, int $y1, int $x2, int $y2, int $color): static;

    public function fillTriangle(int $x0, int $y0, int $x1, int $y1, int $x2, int $y2, int $color): static;

    public function setCursor(int $x, int $y): static;

    public function setTextSize(int $s, ?int $y = null): static;

    public function setTextColor(int $color, ?int $bg = null): static;

    public function setTextWrap(bool $wrap): static;

    public function setCp437(bool $enable): static;

    /**
     * @param  GFXFont|string|null  $font  null / 'classic' = built-in 5×7; string = Font registry slug
     */
    public function setFont(GFXFont|string|null $font = null): static;

    public function write(int $c): static;

    public function drawChar(int $x, int $y, int $c, int $color, int $bg, int $size_x, int $size_y): static;

    public function print(string $str): static;

    public function println(string $str = ''): static;

    /**
     * @return array{x1: int, y1: int, w: int, h: int}
     */
    public function getTextBounds(string $str, int $x, int $y): array;
}
