<?php

namespace ScrapyardIO\Tubes\HumanInput;

use GeneralPurposeIO\Core\MagicAliases\Circuit;
use Waveforms\Contracts\Actuation\HumanInput\GameController as WaveformsGameController;
use Waveforms\Contracts\Actuation\HumanInput\GameControllerAxis;
use Waveforms\Contracts\Actuation\Interfaces\ButtonPad as WaveformsButtonPad;

/**
 * Circuit / GPIO human-input host — sibling of {@see EngineInput}.
 *
 * Maps Waveforms ButtonPad / GameController circuit profiles into Tubes devices.
 */
class CircuitInput extends HumanInput
{
    /** @var list<WaveformsButtonPad> */
    protected array $circuits = [];

    /** @var list<GamePad> */
    protected array $game_pads = [];

    /** @var list<GameController> */
    protected array $game_controllers = [];

    /**
     * @var list<array{
     *     circuit: WaveformsButtonPad,
     *     pad: ?GamePad,
     *     controller: ?GameController,
     *     digital: array<string, DigitalButton>,
     *     left_stick: ?AnalogStick,
     *     right_stick: ?AnalogStick,
     *     left_trigger: ?AnalogButton,
     *     right_trigger: ?AnalogButton
     * }>
     */
    protected array $bindings = [];

    public function __construct(WaveformsButtonPad ...$circuits)
    {
        foreach ($circuits as $circuit) {
            $this->bindCircuit($circuit);
        }
    }

    public static function profile(string ...$profiles): static
    {
        if ($profiles === []) {
            throw HumanInputException::circuitInputRequiresProfiles();
        }

        $circuits = [];
        foreach ($profiles as $profile) {
            $resolved = Circuit::profile($profile);
            if (! $resolved instanceof WaveformsButtonPad) {
                throw HumanInputException::circuitProfileNotButtonPad($profile);
            }
            $circuits[] = $resolved;
        }

        return new static(...$circuits);
    }

    /**
     * @param  list<WaveformsButtonPad>  $circuits
     */
    public static function fromCircuits(array $circuits): static
    {
        return new static(...$circuits);
    }

    public function poll(): static
    {
        foreach ($this->bindings as $binding) {
            $circuit = $binding['circuit'];
            $circuit->poll();

            foreach ($binding['digital'] as $label => $button) {
                $button->setPressed($circuit->isDown($label));
            }

            if ($circuit instanceof WaveformsGameController) {
                $binding['left_stick']?->setAxes(
                    $circuit->axis(GameControllerAxis::LEFT_X),
                    $circuit->axis(GameControllerAxis::LEFT_Y),
                );
                $binding['right_stick']?->setAxes(
                    $circuit->axis(GameControllerAxis::RIGHT_X),
                    $circuit->axis(GameControllerAxis::RIGHT_Y),
                );
                $binding['left_trigger']?->setValue(
                    max(0.0, min(1.0, $circuit->axis(GameControllerAxis::LEFT_TRIGGER))),
                );
                $binding['right_trigger']?->setValue(
                    max(0.0, min(1.0, $circuit->axis(GameControllerAxis::RIGHT_TRIGGER))),
                );
            }
        }

        return $this;
    }

    public function keyboard(): ?Keyboard
    {
        return null;
    }

    public function mouse(): ?Mouse
    {
        return null;
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

    /**
     * @return list<WaveformsButtonPad>
     */
    public function circuits(): array
    {
        return $this->circuits;
    }

    protected function bindCircuit(WaveformsButtonPad $circuit): void
    {
        $this->circuits[] = $circuit;
        $name = $circuit::class;
        $digital = [];
        $controls = [];

        foreach ($circuit->labels() as $label) {
            $button = new DigitalButton($label, $circuit->isDown($label));
            $digital[$label] = $button;
            $controls[] = $button;
        }

        if ($circuit instanceof WaveformsGameController) {
            $left = new AnalogStick(
                'left',
                $circuit->axis(GameControllerAxis::LEFT_X),
                $circuit->axis(GameControllerAxis::LEFT_Y),
            );
            $right = new AnalogStick(
                'right',
                $circuit->axis(GameControllerAxis::RIGHT_X),
                $circuit->axis(GameControllerAxis::RIGHT_Y),
            );
            $lt = new AnalogButton(
                'left_trigger',
                max(0.0, min(1.0, $circuit->axis(GameControllerAxis::LEFT_TRIGGER))),
            );
            $rt = new AnalogButton(
                'right_trigger',
                max(0.0, min(1.0, $circuit->axis(GameControllerAxis::RIGHT_TRIGGER))),
            );
            $controls[] = $left;
            $controls[] = $right;
            $controls[] = $lt;
            $controls[] = $rt;

            $controller = new GameController($name, $controls);
            $this->game_controllers[] = $controller;
            $this->bindings[] = [
                'circuit' => $circuit,
                'pad' => null,
                'controller' => $controller,
                'digital' => $digital,
                'left_stick' => $left,
                'right_stick' => $right,
                'left_trigger' => $lt,
                'right_trigger' => $rt,
            ];

            return;
        }

        $pad = new GamePad($name, $controls);
        $this->game_pads[] = $pad;
        $this->bindings[] = [
            'circuit' => $circuit,
            'pad' => $pad,
            'controller' => null,
            'digital' => $digital,
            'left_stick' => null,
            'right_stick' => null,
            'left_trigger' => null,
            'right_trigger' => null,
        ];
    }
}
