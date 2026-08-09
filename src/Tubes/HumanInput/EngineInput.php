<?php

namespace ScrapyardIO\Tubes\HumanInput;

use ScrapyardIO\Tubes\Inputs\InputHandler;

/**
 * Engine human-input host — thin wrapper over a companion {@see InputHandler}.
 *
 * Mirrors {@see \ScrapyardIO\Tubes\Canvas\OSWindow} wrapping {@see \ScrapyardIO\Tubes\Windows\WindowHandler}.
 */
class EngineInput extends HumanInput
{
    public function __construct(
        protected InputHandler $handler,
    ) {}

    public function handler(): InputHandler
    {
        return $this->handler;
    }

    public function poll(): static
    {
        $this->handler->poll();

        return $this;
    }

    public function keyboard(): ?Keyboard
    {
        return $this->handler->keyboard();
    }

    public function mouse(): ?Mouse
    {
        return $this->handler->mouse();
    }

    /**
     * @return list<GamePad>
     */
    public function gamePads(): array
    {
        return $this->handler->gamePads();
    }

    /**
     * @return list<GameController>
     */
    public function gameControllers(): array
    {
        return $this->handler->gameControllers();
    }
}
