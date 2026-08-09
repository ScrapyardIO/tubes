<?php

use Fabricate\Sketches\Flow\AsyncFlow;
use Fabricate\Sketches\Flow\AsyncNode;
use ScrapyardIO\Tubes\Core\Runner\Sketches\Workflows\BallPhysicsNode;
use ScrapyardIO\Tubes\Core\Runner\Sketches\Workflows\MetalCanvasFlow;

it('wires MetalCanvasFlow as AsyncFlow with a fiber BallPhysicsNode', function () {
    $flow = MetalCanvasFlow::make();
    expect($flow)->toBeInstanceOf(AsyncFlow::class);

    $physics = new BallPhysicsNode(concurrencyDriver: 'fiber');
    expect($physics)->toBeInstanceOf(AsyncNode::class);

    $driverProp = new \ReflectionProperty(AsyncNode::class, 'concurrencyDriver');
    expect($driverProp->getValue($physics))->toBe('fiber');
});

it('advances ball pose with delta time (px/s)', function () {
    $node = new BallPhysicsNode(concurrencyDriver: 'fiber');

    $shared = [
        'width' => 200,
        'height' => 120,
        'restitution' => 0.85,
        'fps' => 60,
        'dt_override' => 1.0 / 60.0,
        'ball' => [
            'x' => 100.0,
            'y' => 60.0,
            'vx' => 426.0,
            'vy' => 144.0,
            'r' => 10,
        ],
    ];

    $prep = $node->prepAsync($shared);
    $exec = $node->execAsync($prep);
    $action = $node->postAsync($shared, $prep, $exec);

    expect($action)->toBe('default')
        ->and($shared['dt'])->toBe(1.0 / 60.0)
        ->and($shared['ball']['x'])->toBeGreaterThan(100.0)
        ->and($shared['ball']['x'])->toEqualWithDelta(100.0 + (426.0 / 60.0), 0.001);
});

it('scales displacement with larger dt', function () {
    $node = new BallPhysicsNode(concurrencyDriver: 'fiber');

    $step = function (float $dt) use ($node): float {
        $shared = [
            'width' => 800,
            'height' => 600,
            'restitution' => 1.0,
            'fps' => 60,
            'dt_override' => $dt,
            'ball' => [
                'x' => 100.0,
                'y' => 300.0,
                'vx' => 300.0,
                'vy' => 0.0,
                'r' => 10,
            ],
        ];

        $prep = $node->prepAsync($shared);
        $exec = $node->execAsync($prep);
        $node->postAsync($shared, $prep, $exec);

        return (float) $shared['ball']['x'];
    };

    $xShort = $step(1.0 / 60.0);
    $xLong = $step(1.0 / 30.0);

    expect($xLong - 100.0)->toEqualWithDelta(($xShort - 100.0) * 2.0, 0.001);
});

it('stamps acceleration (Δv/dt) on shared ball state after a bounce', function () {
    $node = new BallPhysicsNode(concurrencyDriver: 'fiber');

    $shared = [
        'width' => 200,
        'height' => 120,
        'restitution' => 1.0,
        'fps' => 60,
        'dt_override' => 1.0 / 60.0,
        'ball' => [
            'x' => 10.0,
            'y' => 60.0,
            'vx' => -300.0,
            'vy' => 0.0,
            'r' => 10,
        ],
    ];

    $prep = $node->prepAsync($shared);
    $exec = $node->execAsync($prep);
    $node->postAsync($shared, $prep, $exec);

    expect($shared['ball'])->toHaveKeys(['ax', 'ay'])
        ->and($shared['ball']['ax'])->not->toBe(0.0)
        ->and($shared['ball']['vx'])->toBeGreaterThan(0.0);
});
