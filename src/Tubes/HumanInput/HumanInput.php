<?php

namespace ScrapyardIO\Tubes\HumanInput;

/**
 * Abstract human-input host — Engine | Circuit siblings under this type.
 *
 * Devices (Keyboard, Mouse, GamePad, GameController) are mediums, not subclasses.
 */
abstract class HumanInput
{
    abstract public function poll(): static;

    abstract public function keyboard(): ?Keyboard;

    abstract public function mouse(): ?Mouse;

    /**
     * @return list<GamePad>
     */
    abstract public function gamePads(): array;

    /**
     * @return list<GameController>
     */
    abstract public function gameControllers(): array;
}
