<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MagicAlias default drivers
    |--------------------------------------------------------------------------
    |
    | Used when Window::driver() / Framebuffer::driver() / Font::driver()
    | (or Font::font()) are called without a name. Subsystem configs
    | (windows.default, framebuffers.default, fonts.default) mirror these
    | unless the app overrides them after publish.
    |
    */

    'defaults' => [
        'window' => env('WINDOW_DRIVER', 'sdl3'),
        'framebuffer' => env('FRAMEBUFFER_DRIVER', 'full'),
        'font' => env('FONT_DRIVER', 'classic'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Canvas profiles
    |--------------------------------------------------------------------------
    |
    | Segmented host (windows) vs IC (panels) canvas presets.
    |
    | Windows: Window::profile('my-profile') hydrates a PendingWindow from
    | tubes.canvas_profiles.windows.my-profile. You may also pass a dotted
    | config path: Window::profile('tubes.canvas_profiles.windows.my-profile').
    |
    | Supported window keys:
    |   driver (required unless using MagicAlias default)
    |   title, width, height  — or resolution as [w, h] / "WxH"
    |   options (array) — merged into PendingWindow options
    |   any other scalar keys are copied into options
    |
    | Panels: reserved for PanelIC / GPIO restore (no factory API yet).
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
            // Example (not wired until PanelIC factory lands):
            // 'oled-128x64' => [
            //     'driver' => 'ssd1306',
            //     'width' => 128,
            //     'height' => 64,
            // ],
        ],

    ],

];
