<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | framebuffer / font — MagicAlias driver slugs when called without a name
    |   (synced to framebuffers.default / fonts.default).
    |
    | canvas — any profile slug under canvas_profiles.windows or .panels.
    |   Package default points at the window demo profile; apps override
    |   (e.g. tubes-dev → st7796-front).
    |
    */

    'defaults' => [
        'framebuffer' => env('FRAMEBUFFER_DRIVER', 'full'),
        'font' => env('FONT_DRIVER', 'classic'),
        'canvas' => env('TUBES_CANVAS', 'canvas-window-demo'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Canvas profiles
    |--------------------------------------------------------------------------
    |
    | Segmented host (windows) vs IC (panels) canvas presets.
    |
    | Windows: Window::profile('my-profile')
    | Panels: Panel::profile($name) → Circuit::profile(circuit) + renderer.
    |   CPU: circuit + renderer + framebuffer (page|full|dirty) — required.
    |   Engine: circuit + renderer only (omit framebuffer).
    |
    */

    'canvas_profiles' => [

        'windows' => [

            'demo' => [
                'driver' => 'sdl3',
                'title' => 'Tubes Demo',
                'width' => 800,
                'height' => 600,
            ],

            'canvas-window-demo' => [
                'driver' => 'metal',
                'title' => 'Tubes Canvas',
                'width' => 800,
                'height' => 600,
            ],

            // BC alias for the sketch profile above.
            'metal-canvas' => [
                'driver' => 'metal',
                'title' => 'Tubes Canvas',
                'width' => 800,
                'height' => 600,
            ],

        ],

        'panels' => [
            // CPU: circuit + renderer + framebuffer (page|full|dirty) — framebuffer REQUIRED
            // 'oled-front' => [
            //     'circuit' => 'oled_front',
            //     'renderer' => \Microscrap\GFX\PhpdaFruit\PhpdafruitRenderer2D::class,
            //     'framebuffer' => 'page',
            // ],
            // 'st7796-front' => [
            //     'circuit' => 'st7796',
            //     'renderer' => \Microscrap\GFX\PhpdaFruit\PhpdafruitRenderer2D::class,
            //     'framebuffer' => 'dirty',
            // ],
            // Engine: circuit + renderer only (omit framebuffer)
            // 'st7796-vulkan' => [
            //     'circuit' => 'st7796',
            //     'renderer' => \Microscrap\GFX\Vulkan\VulkanRenderer2D::class,
            // ],
        ],

    ],

];
