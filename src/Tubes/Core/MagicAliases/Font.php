<?php

namespace ScrapyardIO\Tubes\Core\MagicAliases;

use Fabricate\MagicAliases\MagicAlias;

/**
 * Magic alias for the tubes font registry.
 *
 * Never caches a GFXFont instance — the accessor resolves the manager.
 *
 * @method static \ScrapyardIO\Tubes\Fonts\FontManager extend(string $name, string $class)
 * @method static \ScrapyardIO\Tubes\Fonts\FontManager addFont(string $name, string $class)
 * @method static \ScrapyardIO\Tubes\Contracts\Fonts\GFXFont font(?string $name = null)
 * @method static \ScrapyardIO\Tubes\Contracts\Fonts\GFXFont driver(?string $name = null)
 * @method static string defaultFont()
 * @method static string defaultDriver()
 * @method static bool hasFont(string $name)
 * @method static array listFonts()
 *
 * @see \ScrapyardIO\Tubes\Fonts\FontManager
 */
class Font extends MagicAlias
{
    protected static function getMagicAliasAccessor(): string
    {
        return 'font';
    }
}
