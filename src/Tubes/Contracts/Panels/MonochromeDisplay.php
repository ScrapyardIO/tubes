<?php

namespace ScrapyardIO\Tubes\Contracts\Panels;

/**
 * Qualifies a {@see PanelDevice} as a 1-bit / page-packed monochrome panel IC.
 *
 * Implemented by chip packages (e.g. SSD1306). Tubes wraps implementors in
 * {@see \ScrapyardIO\Tubes\Panels\MonochromeDisplay}.
 */
interface MonochromeDisplay extends PanelDevice
{
}
