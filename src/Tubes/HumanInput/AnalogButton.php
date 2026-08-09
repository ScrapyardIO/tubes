<?php

namespace ScrapyardIO\Tubes\HumanInput;

class AnalogButton
{
    public function __construct(
        protected string $name,
        protected float $value = 0.0,
    ) {
        $this->assertRange($value);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(): float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->assertRange($value);
        $this->value = $value;

        return $this;
    }

    protected function assertRange(float $value): void
    {
        if ($value < 0.0 || $value > 1.0) {
            throw HumanInputException::analogButtonOutOfRange($value);
        }
    }
}
