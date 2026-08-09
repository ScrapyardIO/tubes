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

it('advances ball pose through prep/exec/post without a container', function () {
    $node = new BallPhysicsNode(concurrencyDriver: 'fiber');

    $shared = [
        'width' => 200,
        'height' => 120,
        'restitution' => 0.85,
        'fps' => 60,
        'ball' => [
            'x' => 100.0,
            'y' => 60.0,
            'vx' => 7.1,
            'vy' => 2.4,
            'r' => 10,
        ],
    ];

    $prep = $node->prepAsync($shared);
    $exec = $node->execAsync($prep);
    $action = $node->postAsync($shared, $prep, $exec);

    expect($action)->toBe('default')
        ->and($shared['ball']['x'])->not->toBe(100.0);
});

it('stamps per-frame acceleration on shared ball state', function () {
    $node = new BallPhysicsNode(concurrencyDriver: 'fiber');

    $shared = [
        'width' => 200,
        'height' => 120,
        'restitution' => 1.0,
        'ball' => [
            'x' => 10.0,
            'y' => 60.0,
            'vx' => -5.0,
            'vy' => 0.0,
            'r' => 10,
        ],
    ];

    $prep = $node->prepAsync($shared);
    $exec = $node->execAsync($prep);
    $node->postAsync($shared, $prep, $exec);

    expect($shared['ball'])->toHaveKeys(['ax', 'ay'])
        ->and($shared['ball']['ax'])->not->toBe(0.0);
});
