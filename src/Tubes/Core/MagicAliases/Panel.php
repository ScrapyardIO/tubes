<?php

namespace ScrapyardIO\Tubes\Core\MagicAliases;

use Fabricate\MagicAliases\MagicAlias;

/**
 * Magic alias for the tubes panel factory.
 *
 * @method static \ScrapyardIO\Tubes\Panels\PendingPanel driver(?string $driver = null)
 * @method static \ScrapyardIO\Tubes\Panels\PendingPanel make(?string $driver = null)
 * @method static \ScrapyardIO\Tubes\Canvas\PanelIC profile(string $name)
 * @method static \ScrapyardIO\Tubes\Canvas\PanelIC wrap(\ScrapyardIO\Tubes\Contracts\Panels\PanelDevice $ic, \ScrapyardIO\Tubes\Rendering\Renderer2D $renderer, ?string $framebufferDriver = null)
 *
 * @see \ScrapyardIO\Tubes\Panels\PanelManager
 */
class Panel extends MagicAlias
{
    protected static function getMagicAliasAccessor(): string
    {
        return 'panel';
    }
}
