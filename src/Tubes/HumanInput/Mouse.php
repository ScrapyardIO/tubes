<?php

namespace ScrapyardIO\Tubes\HumanInput;

use ScrapyardIO\Tubes\HumanInput\Enums\MouseButton;

class Mouse
{
    /**
     * @param  list<DigitalButton>  $buttons
     */
    public function __construct(
        protected float $x = 0.0,
        protected float $y = 0.0,
        protected array $buttons = [],
        protected float $wheel_delta = 0.0,
    ) {}

    public function x(): float
    {
        return $this->x;
    }

    public function y(): float
    {
        return $this->y;
    }

    public function position(): array
    {
        return [$this->x, $this->y];
    }

    public function setPosition(float $x, float $y): static
    {
        $this->x = $x;
        $this->y = $y;

        return $this;
    }

    /**
     * @return list<DigitalButton>
     */
    public function buttons(): array
    {
        return $this->buttons;
    }

    /**
     * @param  list<DigitalButton>  $buttons
     */
    public function setButtons(array $buttons): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function button(string|MouseButton $name): ?DigitalButton
    {
        $key = $name instanceof MouseButton ? $name->value : $name;

        foreach ($this->buttons as $button) {
            if ($button->name() === $key) {
                return $button;
            }
        }

        return null;
    }

    public function isPressed(string|MouseButton $name): bool
    {
        $button = $this->button($name);

        return ! is_null($button) && $button->isPressed();
    }

    public function wheelDelta(): float
    {
        return $this->wheel_delta;
    }

    public function setWheelDelta(float $delta): static
    {
        $this->wheel_delta = $delta;

        return $this;
    }
}
