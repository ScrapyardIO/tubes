<?php

use ScrapyardIO\Tubes\HumanInput\CircuitInput;
use ScrapyardIO\Tubes\HumanInput\HumanInputException;
use Waveforms\Contracts\Actuation\HumanInput\GameController as WaveformsGameController;
use Waveforms\Contracts\Actuation\HumanInput\GameControllerAxis;
use Waveforms\Contracts\Actuation\Interfaces\Button;
use Waveforms\Contracts\Actuation\Interfaces\ButtonPad as WaveformsButtonPad;

final class FakeWaveformsButton implements Button
{
    public function __construct(
        protected string $label,
        public bool $down = false,
    ) {}

    public function label(): string
    {
        return $this->label;
    }

    public function poll(): static
    {
        return $this;
    }

    public function isDown(): bool
    {
        return $this->down;
    }

    public function isPressed(): bool
    {
        return false;
    }

    public function wasReleased(): bool
    {
        return false;
    }

    public function isHolding(): bool
    {
        return false;
    }

    public function heldMs(): int
    {
        return 0;
    }

    public function holdMs(): int
    {
        return 500;
    }

    public function setHoldMs(int $hold_ms): static
    {
        return $this;
    }

    public function history(): array
    {
        return [];
    }

    public function clearHistory(): static
    {
        return $this;
    }

    public function close(): void {}
}

class FakeWaveformsButtonPad implements WaveformsButtonPad
{
    /** @var array<string, FakeWaveformsButton> */
    public array $buttons = [];

    public int $polls = 0;

    /**
     * @param  list<string>  $labels
     */
    public function __construct(array $labels)
    {
        foreach ($labels as $label) {
            $this->buttons[$label] = new FakeWaveformsButton($label);
        }
    }

    public function poll(): static
    {
        $this->polls++;

        return $this;
    }

    public function buttons(): array
    {
        return $this->buttons;
    }

    public function labels(): array
    {
        return array_keys($this->buttons);
    }

    public function button(string $label): Button
    {
        return $this->buttons[$label];
    }

    public function has(string $label): bool
    {
        return isset($this->buttons[$label]);
    }

    public function isDown(string $label): bool
    {
        return $this->buttons[$label]->isDown();
    }

    public function isPressed(string $label): bool
    {
        return false;
    }

    public function wasReleased(string $label): bool
    {
        return false;
    }

    public function isHolding(string $label): bool
    {
        return false;
    }

    public function downLabels(): array
    {
        return array_values(array_filter($this->labels(), fn (string $l): bool => $this->isDown($l)));
    }

    public function pressedLabels(): array
    {
        return [];
    }

    public function holdingLabels(): array
    {
        return [];
    }

    public function anyDown(string ...$labels): bool
    {
        return false;
    }

    public function allDown(string ...$labels): bool
    {
        return false;
    }

    public function chord(string ...$labels): bool
    {
        return false;
    }

    public function anyPressed(string ...$labels): bool
    {
        return false;
    }

    public function close(): void {}
}

final class FakeWaveformsGameController extends FakeWaveformsButtonPad implements WaveformsGameController
{
    /** @var array<string, float> */
    public array $axes = [
        'left_x' => 0.0,
        'left_y' => 0.0,
        'right_x' => 0.0,
        'right_y' => 0.0,
        'left_trigger' => 0.0,
        'right_trigger' => 0.0,
    ];

    public function connected(): bool
    {
        return true;
    }

    public function axis(GameControllerAxis $axis): float
    {
        return $this->axes[$axis->value] ?? 0.0;
    }

    public function axes(): array
    {
        return $this->axes;
    }
}

test('CircuitInput maps ButtonPad-only circuits to GamePad devices', function () {
    $pad = new FakeWaveformsButtonPad(['a', 'b']);
    $pad->buttons['a']->down = true;

    $input = CircuitInput::fromCircuits([$pad]);

    expect($input->gamePads())->toHaveCount(1)
        ->and($input->gameControllers())->toBe([])
        ->and($input->gamePads()[0]->digitalButtons())->toHaveCount(2)
        ->and($input->gamePads()[0]->digitalButtons()[0]->isPressed())->toBeTrue();
});

test('CircuitInput maps Waveforms GameController to Tubes GameController with sticks and triggers', function () {
    $wf = new FakeWaveformsGameController(['south']);
    $wf->axes['left_x'] = 0.5;
    $wf->axes['left_y'] = -0.25;
    $wf->axes['left_trigger'] = 0.75;

    $input = CircuitInput::fromCircuits([$wf]);

    expect($input->gameControllers())->toHaveCount(1)
        ->and($input->gamePads())->toBe([])
        ->and($input->gameControllers()[0]->sticks())->toHaveCount(2)
        ->and($input->gameControllers()[0]->sticks()[0]->x())->toBe(0.5)
        ->and($input->gameControllers()[0]->sticks()[0]->y())->toBe(-0.25)
        ->and($input->gameControllers()[0]->analogButtons()[0]->value())->toBe(0.75);
});

test('CircuitInput poll re-polls circuits and updates device controls in place', function () {
    $wf = new FakeWaveformsGameController(['a']);
    $input = CircuitInput::fromCircuits([$wf]);
    $stick = $input->gameControllers()[0]->sticks()[0];
    $button = $input->gameControllers()[0]->digitalButtons()[0];

    $wf->buttons['a']->down = true;
    $wf->axes['left_x'] = 1.0;
    $wf->axes['left_y'] = -1.0;

    $input->poll();

    expect($wf->polls)->toBe(1)
        ->and($button->isPressed())->toBeTrue()
        ->and($stick->x())->toBe(1.0)
        ->and($stick->y())->toBe(-1.0);
});

test('CircuitInput::profile requires at least one profile name', function () {
    expect(fn () => CircuitInput::profile())
        ->toThrow(HumanInputException::class, HumanInputException::circuitInputRequiresProfiles()->getMessage());
});
