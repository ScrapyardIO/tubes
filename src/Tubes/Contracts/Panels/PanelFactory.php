<?php

namespace ScrapyardIO\Tubes\Contracts\Panels;

use ScrapyardIO\Tubes\Canvas\PanelIC;
use ScrapyardIO\Tubes\Panels\PendingPanel;
use ScrapyardIO\Tubes\Rendering\Renderer2D;

interface PanelFactory
{
    /**
     * Start a fluent build. Optional driver selects managed framebuffer kind
     * (`page`, `full`, …); null picks from the IC {@see FormatSpec}.
     *
     * @param  non-empty-string|null  $driver
     */
    public function driver(?string $driver = null): PendingPanel;

    /**
     * @param  non-empty-string|null  $driver
     */
    public function make(?string $driver = null): PendingPanel;

    /**
     * Hydrate from tubes.canvas_profiles.panels.{name} (requires `circuit` + `renderer`).
     *
     * @param  non-empty-string  $name
     */
    public function profile(string $name): PanelIC;

    /**
     * Wrap a contract-qualified IC with a Renderer2D immediately.
     */
    public function wrap(PanelDevice $ic, Renderer2D $renderer, ?string $framebufferDriver = null): PanelIC;
}
