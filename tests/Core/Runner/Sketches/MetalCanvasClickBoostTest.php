<?php

use ScrapyardIO\Tubes\Core\Runner\Sketches\Support\MetalCanvasClickBoost;
use ScrapyardIO\Tubes\Core\Runner\Sketches\Workflows\BallPhysicsNode;
use ScrapyardIO\Tubes\HumanInput\DigitalButton;
use ScrapyardIO\Tubes\HumanInput\EngineInput;
use ScrapyardIO\Tubes\HumanInput\Enums\MouseButton;
use ScrapyardIO\Tubes\HumanInput\Mouse;
use ScrapyardIO\Tubes\Inputs\InputHandler;

it('hit-tests the ball circle', function () {
    $ball = ['x' => 100.0, 'y' => 80.0, 'r' => 24];

    expect(MetalCanvasClickBoost::hitsBall(100.0, 80.0, $ball))->toBeTrue()
        ->and(MetalCanvasClickBoost::hitsBall(124.0, 80.0, $ball))->toBeTrue()
        ->and(MetalCanvasClickBoost::hitsBall(125.0, 80.0, $ball))->toBeFalse();
});

it('keeps boost active for three seconds', function () {
    $now = 1_000.0;
    $until = MetalCanvasClickBoost::boostUntilFromNow($now);

    expect(MetalCanvasClickBoost::durationSeconds())->toBe(3.0)
        ->and(MetalCanvasClickBoost::isActive($until, $now + 2.9))->toBeTrue()
        ->and(MetalCanvasClickBoost::isActive($until, $now + 3.0))->toBeFalse()
        ->and(MetalCanvasClickBoost::remainingSeconds($until, $now + 1.5))->toBe(1.5);
});

it('applies a small acceleration along velocity', function () {
    [$ax, $ay] = MetalCanvasClickBoost::acceleration(240.0, 0.0);

    expect($ax)->toBe(MetalCanvasClickBoost::accelPerSecond())
        ->and($ay)->toBe(0.0);
});

it('starts a click boost when left-click hits the ball via EngineInput', function () {
    $handler = new class extends InputHandler
    {
        public function poll(): static
        {
            return $this;
        }

        public function setMouse(Mouse $mouse): void
        {
            $this->mouse = $mouse;
        }
    };

    $handler->setMouse(new Mouse(
        x: 100.0,
        y: 60.0,
        buttons: [new DigitalButton(MouseButton::LEFT->value, true)],
    ));

    $node = new BallPhysicsNode(concurrencyDriver: 'fiber');
    $shared = [
        'width' => 200,
        'height' => 120,
        'restitution' => 1.0,
        'fps' => 60,
        'dt_override' => 1.0 / 60.0,
        'engine_input' => new EngineInput($handler),
        'mouse_left_was_pressed' => false,
        'ball' => [
            'x' => 100.0,
            'y' => 60.0,
            'vx' => 240.0,
            'vy' => 0.0,
            'r' => 10,
        ],
    ];

    $speedBefore = 240.0;
    $prep = $node->prepAsync($shared);
    $exec = $node->execAsync($prep);
    $node->postAsync($shared, $prep, $exec);

    expect($shared['click_boost_until'] ?? null)->toBeFloat()
        ->and($shared['click_boost_until'])->toBeGreaterThan(microtime(true))
        ->and($shared['ball']['vx'])->toBeGreaterThan($speedBefore)
        ->and($shared['click_boost_remaining'])->toBeGreaterThan(0.0)
        ->and($shared['dt'])->toBe(1.0 / 60.0);
});

it('ignores left-click misses outside the ball', function () {
    $handler = new class extends InputHandler
    {
        public function poll(): static
        {
            return $this;
        }

        public function setMouse(Mouse $mouse): void
        {
            $this->mouse = $mouse;
        }
    };

    $handler->setMouse(new Mouse(
        x: 10.0,
        y: 10.0,
        buttons: [new DigitalButton(MouseButton::LEFT->value, true)],
    ));

    $node = new BallPhysicsNode(concurrencyDriver: 'fiber');
    $shared = [
        'width' => 200,
        'height' => 120,
        'restitution' => 1.0,
        'fps' => 60,
        'dt_override' => 1.0 / 60.0,
        'engine_input' => new EngineInput($handler),
        'mouse_left_was_pressed' => false,
        'ball' => [
            'x' => 100.0,
            'y' => 60.0,
            'vx' => 240.0,
            'vy' => 0.0,
            'r' => 10,
        ],
    ];

    $prep = $node->prepAsync($shared);
    $exec = $node->execAsync($prep);
    $node->postAsync($shared, $prep, $exec);

    expect($shared['click_boost_until'] ?? null)->toBeNull()
        ->and($shared['ball']['vx'])->toBe(240.0);
});
