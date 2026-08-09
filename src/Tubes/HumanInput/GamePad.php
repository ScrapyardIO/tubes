<?php

namespace ScrapyardIO\Tubes\HumanInput;

class GamePad
{
    /** @var list<DigitalButton> */
    protected array $digital_buttons = [];

    /** @var list<AnalogButton> */
    protected array $analog_buttons = [];

    /**
     * @param  list<DigitalButton|AnalogButton|AnalogStick>  $controls
     */
    public function __construct(
        protected string $name,
        array $controls = [],
    ) {
        foreach ($controls as $control) {
            if ($control instanceof AnalogStick) {
                throw HumanInputException::gamePadRejectsSticks();
            }

            if ($control instanceof DigitalButton) {
                $this->digital_buttons[] = $control;
            } elseif ($control instanceof AnalogButton) {
                $this->analog_buttons[] = $control;
            }
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
}
