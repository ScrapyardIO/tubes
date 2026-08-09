<?php

namespace ScrapyardIO\Tubes\HumanInput;

class GameController
{
    /** @var list<DigitalButton> */
    protected array $digital_buttons = [];

    /** @var list<AnalogButton> */
    protected array $analog_buttons = [];

    /** @var list<AnalogStick> */
    protected array $sticks = [];

    /**
     * @param  list<DigitalButton|AnalogButton|AnalogStick>  $controls
     */
    public function __construct(
        protected string $name,
        array $controls = [],
    ) {
        foreach ($controls as $control) {
            if ($control instanceof AnalogStick) {
                $this->sticks[] = $control;
            } elseif ($control instanceof DigitalButton) {
                $this->digital_buttons[] = $control;
            } elseif ($control instanceof AnalogButton) {
                $this->analog_buttons[] = $control;
            }
        }

        $has_stick = count($this->sticks) >= 1;
        $has_button = count($this->digital_buttons) + count($this->analog_buttons) >= 1;

        if (! $has_stick || ! $has_button) {
            throw HumanInputException::gameControllerRequiresStickAndButton();
        }
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return list<DigitalButton>
     */
    public function digitalButtons(): array
    {
        return $this->digital_buttons;
    }

    /**
     * @return list<AnalogButton>
     */
    public function analogButtons(): array
    {
        return $this->analog_buttons;
    }

    /**
     * @return list<AnalogStick>
     */
    public function sticks(): array
    {
        return $this->sticks;
    }
}
