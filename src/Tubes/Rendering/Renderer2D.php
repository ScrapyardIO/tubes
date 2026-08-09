<?php

namespace ScrapyardIO\Tubes\Rendering;

use ScrapyardIO\Tubes\Contracts\Rendering\DrawingAPI;
use ScrapyardIO\Tubes\Contracts\Rendering\RenderingException;

/**
 * Abstract 2D drawing engine. Every microscrap `*-gfx` package subclasses this
 * and overrides {@see DrawingAPI} methods against the borrowed framebuffer.
 *
 * Defaults throw {@see RenderingException::notImplemented()} so empty companion
 * stubs remain loadable until each gfx package fills them in.
 *
 * Soft sketch path may use {@see Concerns\DrawsText}. 3D belongs in a game-engine package.
 */
abstract class Renderer2D extends Renderer implements DrawingAPI
{
    public function drawPixel(int $x, int $y, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    /**
     * @param  array<int, array{0: int, 1: int, 2: int}>  $pixels
     */
    public function drawPixels(array $pixels): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function drawSegment(int $x, int $y, int $width, int $height, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function drawLine(int $x0, int $y0, int $x1, int $y1, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function drawHorizontalLine(int $x, int $y, int $w, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function drawVerticalLine(int $x, int $y, int $h, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    /**
     * @param  array<int, array{0: int, 1: int, 2: int, 3: int, 4: int}>  $lines
     */
    public function drawLines(array $lines): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function drawRect(int $x, int $y, int $w, int $h, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function drawRoundRect(int $x, int $y, int $w, int $h, int $r, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function fill(int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function fillRect(int $x, int $y, int $w, int $h, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function fillRoundRect(int $x, int $y, int $w, int $h, int $r, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function fillCircle(int $x0, int $y0, int $r, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function drawCircle(int $x0, int $y0, int $r, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function drawEllipse(int $x0, int $y0, int $rw, int $rh, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function fillEllipse(int $x0, int $y0, int $rw, int $rh, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function drawTriangle(int $x0, int $y0, int $x1, int $y1, int $x2, int $y2, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function fillTriangle(int $x0, int $y0, int $x1, int $y1, int $x2, int $y2, int $color): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    /**
     * Alias for {@see drawHorizontalLine()} (legacy short name).
     */
    public function drawHLine(int $x, int $y, int $w, int $color): static
    {
        return $this->drawHorizontalLine($x, $y, $w, $color);
    }

    /**
     * Alias for {@see drawVerticalLine()} (legacy short name).
     */
    public function drawVLine(int $x, int $y, int $h, int $color): static
    {
        return $this->drawVerticalLine($x, $y, $h, $color);
    }

    public function setCursor(int $x, int $y): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function setTextSize(int $s, ?int $y = null): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function setTextColor(int $color, ?int $bg = null): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function setTextWrap(bool $wrap): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function setCp437(bool $enable): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function setFont(\ScrapyardIO\Tubes\Contracts\Fonts\GFXFont|string|null $font = null): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function write(int $c): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function drawChar(int $x, int $y, int $c, int $color, int $bg, int $size_x, int $size_y): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function print(string $str): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function println(string $str = ''): static
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }

    public function getTextBounds(string $str, int $x, int $y): array
    {
        throw RenderingException::notImplemented(__FUNCTION__);
    }
}
