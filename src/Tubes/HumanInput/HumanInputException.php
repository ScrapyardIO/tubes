<?php

namespace ScrapyardIO\Tubes\HumanInput;

use InvalidArgumentException;

class HumanInputException extends InvalidArgumentException
{
    public static function gamePadRejectsSticks(): self
    {
        return new self('GamePad rejects AnalogStick controls; use GameController for sticks.');
    }

    public static function gameControllerRequiresStickAndButton(): self
    {
        return new self('GameController requires at least one AnalogStick and one DigitalButton or AnalogButton.');
    }

    public static function analogButtonOutOfRange(float $value): self
    {
        return new self("AnalogButton value must be between 0 and 1 inclusive, got {$value}.");
    }

    public static function analogStickAxisOutOfRange(string $axis, float $value): self
    {
        return new self("AnalogStick {$axis} must be between -1 and 1 inclusive, got {$value}.");
    }

    public static function circuitInputRequiresProfiles(): self
    {
        return new self('CircuitInput::profile() requires at least one circuits.php profile name.');
    }

    public static function circuitProfileNotButtonPad(string $profile): self
    {
        return new self("Circuit profile [{$profile}] did not resolve to a Waveforms ButtonPad or GameController.");
    }
}
