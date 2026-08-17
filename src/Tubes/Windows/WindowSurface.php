<?php

namespace Tubes\Windows;

use Tubes\Contracts\Windows\Exceptions\WindowableException;
use Tubes\Contracts\Windows\WindowSurface as SurfaceContract;
use Tubes\Windows\Enums\FontWeight;
use Tubes\Windows\Enums\TextAlignment;
use Tubes\Windows\Enums\ViewType;

abstract class WindowSurface implements SurfaceContract
{
    protected ?int $content_pointer = null;

    protected int $current_width;
    protected int $current_height;

    /** @var (callable(): void)|null */
    protected mixed $relayout = null;

    /** @var array<string, int> */
    protected array $views = [];

    public function __construct(
        public readonly string $window_name,
        public readonly int $pointer,
        public readonly int $starting_width,
        public readonly int $starting_height,
    ) {
        $this->current_width = $starting_width;
        $this->current_height = $starting_height;
    }

    /**
     * @param  array<string, mixed>  $addl_params
     */
    abstract public function addView(
        string $name,
        ViewType $view_component_enum,
        int $x,
        int $y,
        int $h,
        int $w,
        array $addl_params = []
    ): static;

    /**
     * @param  array<string, mixed>  $addl_params
     */
    abstract public function addLabel(
        string $name,
        int $x,
        int $y,
        int $h,
        int $w,
        array $addl_params = []
    ): static;

    /**
     * @param  array<string, mixed>  $addl_params
     */
    public function addButton(
        string $name,
        int $x,
        int $y,
        int $h,
        int $w,
        array $addl_params = []
    ): static {
        return $this->addView($name, ViewType::BUTTON, $x, $y, $h, $w, $addl_params);
    }

    /**
     * @param  array<string, mixed>  $addl_params
     */
    public function addEntry(
        string $name,
        int $x,
        int $y,
        int $h,
        int $w,
        array $addl_params = []
    ): static {
        return $this->addView($name, ViewType::ENTRY, $x, $y, $h, $w, $addl_params);
    }

    /**
     * @param  array<string, mixed>  $addl_params
     */
    public function addCheckbox(
        string $name,
        int $x,
        int $y,
        int $h,
        int $w,
        array $addl_params = []
    ): static {
        return $this->addView($name, ViewType::CHECKBOX, $x, $y, $h, $w, $addl_params);
    }

    public function getPointer(): int
    {
        return $this->pointer;
    }

    public function getContentPointer(): ?int
    {
        return $this->content_pointer;
    }

    public function setContentPointer(int $content_pointer): static
    {
        $this->content_pointer = $content_pointer;
        return $this;
    }

    public function getCurrentWidth(): int
    {
        return $this->current_width;
    }

    public function getCurrentHeight(): int
    {
        return $this->current_height;
    }

    public function pollResize(): bool
    {
        $width = $this->nativeContentWidth();
        $height = $this->nativeContentHeight();

        if ($width <= 0 || $height <= 0) {
            return false;
        }

        if ($width === $this->current_width && $height === $this->current_height) {
            return false;
        }

        $this->current_width = $width;
        $this->current_height = $height;

        return true;
    }

    /**
     * @param  (callable(): void)|null  $fn
     */
    public function setRelayout(?callable $fn): static
    {
        $this->relayout = $fn;

        return $this;
    }

    abstract protected function nativeContentWidth(): int;

    abstract protected function nativeContentHeight(): int;

    /**
     * @throws WindowableException
     */
    abstract public function setViewFrame(string $name, int $x, int $y, int $h, int $w): static;

    /**
     * @param  array<string, mixed>  $addl_params
     */
    protected function textAlignmentFrom(array $addl_params): ?TextAlignment
    {
        if (! isset($addl_params['alignment'])) {
            return null;
        }

        $value = $addl_params['alignment'];
        if ($value instanceof TextAlignment) {
            return $value;
        }
        if (is_string($value)) {
            return TextAlignment::tryFrom($value);
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $addl_params
     */
    protected function fontSizeFrom(array $addl_params): ?float
    {
        if (! isset($addl_params['font_size'])) {
            return null;
        }

        $value = $addl_params['font_size'];
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $addl_params
     */
    protected function fontWeightFrom(array $addl_params): ?FontWeight
    {
        if (! isset($addl_params['font_weight'])) {
            return null;
        }

        $value = $addl_params['font_weight'];
        if ($value instanceof FontWeight) {
            return $value;
        }
        if (is_int($value)) {
            return FontWeight::tryFrom($value);
        }

        return null;
    }

    /**
     * @throws WindowableException
     */
    protected function rememberView(string $name, int $handle): void
    {
        if (isset($this->views[$name])) {
            throw new WindowableException("View $name already exists.");
        }

        $this->views[$name] = $handle;
    }

    /**
     * @throws WindowableException
     */
    protected function viewHandle(string $name): int
    {
        if (! isset($this->views[$name])) {
            throw new WindowableException("View $name does not exist.");
        }

        return $this->views[$name];
    }
}
