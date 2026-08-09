<?php

use ScrapyardIO\Tubes\Core\Enums\GfxCompanionTarget;
use ScrapyardIO\Tubes\Core\Workflows\GfxInstall\ComposerRequireGfxNode;
use ScrapyardIO\Tubes\Core\Workflows\GfxInstall\DetectHostNode;
use ScrapyardIO\Tubes\Core\Workflows\GfxInstall\EnsureExtensionWrapperNode;
use ScrapyardIO\Tubes\Core\Workflows\GfxInstall\EnsurePhpExtensionNode;
use ScrapyardIO\Tubes\Core\Workflows\GfxInstall\GfxInstallFlow;
use ScrapyardIO\Tubes\Core\Workflows\GfxInstall\PromptGfxTargetsNode;

test('gfx companion matrix has binding wrappers, gfx packages, and publish tags', function () {
    expect(GfxCompanionTarget::SDL3->bindingPackages())->toContain('microscrap/sdl3:^0.7.0')
        ->and(GfxCompanionTarget::SDL3->gfxPackages())->toContain('microscrap/sdl3-gfx:^0.7.0')
        ->and(GfxCompanionTarget::OPEN_GL->framebufferSlug())->toBe('open-gl')
        ->and(GfxCompanionTarget::OPEN_GL->publishTag())->toBe('tubes-framebuffers-open-gl')
        ->and(GfxCompanionTarget::METAL->allowedOsFamilies())->toBe(['darwin'])
        ->and(GfxCompanionTarget::CUDA->allowedOsFamilies())->toBe(['linux'])
        ->and(GfxCompanionTarget::CUDA->isHollow())->toBeFalse()
        ->and(GfxCompanionTarget::CUDA->serviceProvider())->toBe(
            'Microscrap\\GFX\\CUDA\\Providers\\CudaGfxServiceProvider'
        )
        ->and(GfxCompanionTarget::CUDA->bindingPackages())->toContain('microscrap/glfw:^0.7.0')
        ->and(GfxCompanionTarget::CUDA->bindingPackages())->toContain('microscrap/cuda:^0.7.0')
        ->and(GfxCompanionTarget::VULKAN->requiredNativeLibs())->toBe(['vulkan'])
        ->and(GfxCompanionTarget::SDL3->requiredNativeLibs())->toBe(['sdl3'])
        ->and(GfxCompanionTarget::OPEN_GL->requiredNativeLibs())->toBe(['opengl']);
});

test('DetectHostNode denyReason gates Darwin CUDA, linux CUDA when ready, metal OS, and missing libs', function () {
    $node = new DetectHostNode;
    $method = new ReflectionMethod(DetectHostNode::class, 'denyReason');
    $method->setAccessible(true);

    $libs = [
        'sdl3' => true,
        'opengl' => true,
        'vulkan' => true,
        'metal' => true,
        'cuda' => true,
    ];

    expect($method->invoke($node, GfxCompanionTarget::CUDA, 'darwin', 'arm64', $libs))
        ->toBe('unavailable on Darwin')
        ->and($method->invoke($node, GfxCompanionTarget::CUDA, 'linux', 'x86_64', $libs))
        ->toBeNull()
        ->and($method->invoke($node, GfxCompanionTarget::CUDA, 'linux', 'x86_64', ['cuda' => false] + $libs))
        ->toBe('CUDA toolkit not installed')
        ->and($method->invoke($node, GfxCompanionTarget::METAL, 'linux', 'x86_64', $libs))
        ->toContain('unavailable')
        ->and($method->invoke($node, GfxCompanionTarget::SDL3, 'darwin', 'arm64', $libs))
        ->toBeNull()
        ->and($method->invoke($node, GfxCompanionTarget::OPEN_GL, 'darwin', 'arm64', $libs))
        ->toBeNull()
        ->and($method->invoke($node, GfxCompanionTarget::VULKAN, 'darwin', 'arm64', $libs))
        ->toBeNull()
        ->and($method->invoke($node, GfxCompanionTarget::SDL3, 'darwin', 'arm64', ['sdl3' => false] + $libs))
        ->toBe('SDL3 lib not installed')
        ->and($method->invoke($node, GfxCompanionTarget::OPEN_GL, 'darwin', 'arm64', ['opengl' => false] + $libs))
        ->toBe('OpenGL lib not installed')
        ->and($method->invoke($node, GfxCompanionTarget::VULKAN, 'darwin', 'arm64', ['vulkan' => false] + $libs))
        ->toBe('Vulkan lib not installed');
});

test('GfxInstallFlow order is detect → prompt → php ext → wrapper → gfx', function () {
    $flow = GfxInstallFlow::make();
    $startProp = new ReflectionProperty($flow, 'startNode');
    $startProp->setAccessible(true);
    $detect = $startProp->getValue($flow);

    expect($detect)->toBeInstanceOf(DetectHostNode::class);

    $next = new ReflectionMethod($flow, 'getNextNode');
    $next->setAccessible(true);

    $prompt = $next->invoke($flow, $detect, 'default');
    expect($prompt)->toBeInstanceOf(PromptGfxTargetsNode::class);

    $extension = $next->invoke($flow, $prompt, 'default');
    expect($extension)->toBeInstanceOf(EnsurePhpExtensionNode::class);

    $wrapper = $next->invoke($flow, $extension, 'default');
    expect($wrapper)->toBeInstanceOf(EnsureExtensionWrapperNode::class);

    $gfx = $next->invoke($flow, $wrapper, 'default');
    expect($gfx)->toBeInstanceOf(ComposerRequireGfxNode::class);
});
