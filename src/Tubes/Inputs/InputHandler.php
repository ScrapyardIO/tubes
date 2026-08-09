<?php

namespace ScrapyardIO\Tubes\Inputs;

use ScrapyardIO\Tubes\HumanInput\GameController;
use ScrapyardIO\Tubes\HumanInput\GamePad;
use ScrapyardIO\Tubes\HumanInput\Keyboard;
use ScrapyardIO\Tubes\HumanInput\Mouse;

/**
 * Engine-owned human-input driver companion API.
 *
 * Companions (sdl3-gfx, ogx, …) extend this class and fill device state in {@see poll()}.
 */
abstract class InputHandler
{
    protected ?Keyboard $keyboard = null;

    protected ?Mouse $mouse = null;

    /** @var list<GamePad> */
    protected array $game_pads = [];

    /** @var list<GameController> */
    protected array $game_controllers = [];

    abstract public function poll(): static;

    public function keyboard(): ?Keyboard
    {
        return $this->keyboard;
    }

    public function mouse(): ?Mouse
    {
        return $this->mouse;
    }

    /**
     * @return list<GamePad>
     */
    public function gamePads(): array
    {
        return $this->game_pads;
    }

    /**
     * @return list<GameController>
     */
    public function gameControllers(): array
    {
        return $this->game_controllers;
    }
}
