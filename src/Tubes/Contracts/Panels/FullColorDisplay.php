<?php

namespace ScrapyardIO\Tubes\Contracts\Panels;

/**
 * Qualifies a {@see PanelDevice} as a row-major full-color panel IC.
 *
 * Implemented by chip packages (e.g. ST7789). Tubes wraps implementors in
 * {@see \ScrapyardIO\Tubes\Panels\FullColorDisplay}.
 */
interface FullColorDisplay extends PanelDevice
{
}
