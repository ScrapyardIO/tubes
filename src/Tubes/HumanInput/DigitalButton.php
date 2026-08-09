<?php

namespace ScrapyardIO\Tubes\HumanInput;

class DigitalButton
{
    public function __construct(
        protected string $name,
        protected bool $pressed = false,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function isPressed(): bool
    {
        return $this->pressed;
    }

    public function setPressed(bool $pressed): static
    {
        $this->pressed = $pressed;

        return $this;
    }
}
