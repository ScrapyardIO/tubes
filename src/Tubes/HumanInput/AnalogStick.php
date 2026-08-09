<?php

namespace ScrapyardIO\Tubes\HumanInput;

class AnalogStick
{
    public function __construct(
        protected string $name,
        protected float $x = 0.0,
        protected float $y = 0.0,
    ) {
        $this->assertAxis('x', $x);
        $this->assertAxis('y', $y);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function x(): float
    {
        return $this->x;
    }

    public function y(): float
    {
        return $this->y;
    }

    public function setAxes(float $x, float $y): static
    {
        $this->assertAxis('x', $x);
        $this->assertAxis('y', $y);
        $this->x = $x;
        $this->y = $y;

        return $this;
    }

    protected function assertAxis(string $axis, float $value): void
    {
        if ($value < -1.0 || $value > 1.0) {
            throw HumanInputException::analogStickAxisOutOfRange($axis, $value);
        }
    }
}
