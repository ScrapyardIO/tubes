<?php

use ScrapyardIO\Tubes\HumanInput\AnalogButton;
use ScrapyardIO\Tubes\HumanInput\AnalogStick;
use ScrapyardIO\Tubes\HumanInput\DigitalButton;
use ScrapyardIO\Tubes\HumanInput\EngineInput;
use ScrapyardIO\Tubes\HumanInput\GameController;
use ScrapyardIO\Tubes\HumanInput\GamePad;
use ScrapyardIO\Tubes\HumanInput\HumanInputException;
use ScrapyardIO\Tubes\Inputs\InputHandler;

test('GamePad rejects AnalogStick controls', function () {
    expect(fn () => new GamePad('pad', [new AnalogStick('left')]))
        ->toThrow(HumanInputException::class, HumanInputException::gamePadRejectsSticks()->getMessage());
});

test('GamePad accepts digital and analog buttons, including zero controls', function () {
    $empty = new GamePad('empty');
    expect($empty->name())->toBe('empty')
        ->and($empty->digitalButtons())->toBe([])
        ->and($empty->analogButtons())->toBe([]);

    $pad = new GamePad('arcade', [
        new DigitalButton('a'),
        new AnalogButton('trigger', 0.5),
    ]);

    expect($pad->digitalButtons())->toHaveCount(1)
        ->and($pad->analogButtons())->toHaveCount(1)
        ->and($pad->digitalButtons()[0]->name())->toBe('a')
        ->and($pad->analogButtons()[0]->value())->toBe(0.5);
});

test('GameController requires at least one stick and one button', function () {
    expect(fn () => new GameController('bare'))
        ->toThrow(HumanInputException::class, HumanInputException::gameControllerRequiresStickAndButton()->getMessage());

    expect(fn () => new GameController('sticks-only', [new AnalogStick('left')]))
        ->toThrow(HumanInputException::class);

    expect(fn () => new GameController('buttons-only', [new DigitalButton('a')]))
        ->toThrow(HumanInputException::class);
});

test('GameController accepts valid stick plus button composition', function () {
    $controller = new GameController('dual', [
        new AnalogStick('left'),
        new DigitalButton('a'),
        new AnalogButton('lt', 0.25),
        new AnalogStick('right'),
    ]);

    expect($controller->name())->toBe('dual')
        ->and($controller->sticks())->toHaveCount(2)
        ->and($controller->digitalButtons())->toHaveCount(1)
        ->and($controller->analogButtons())->toHaveCount(1);
});

test('EngineInput poll delegates to InputHandler', function () {
    $handler = new class extends InputHandler
    {
        public int $polls = 0;

        public function poll(): static
        {
            $this->polls++;

            return $this;
        }
    };

    $input = new EngineInput($handler);
    $input->poll()->poll();

    expect($handler->polls)->toBe(2)
        ->and($input->handler())->toBe($handler)
        ->and($input->keyboard())->toBeNull()
        ->and($input->mouse())->toBeNull()
        ->and($input->gamePads())->toBe([])
        ->and($input->gameControllers())->toBe([]);
});
