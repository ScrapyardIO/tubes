<?php

namespace ScrapyardIO\Tubes\Core\MagicAliases;

use Fabricate\MagicAliases\MagicAlias;

/**
 * Magic alias for the tubes window factory.
 *
 * Never caches an OSWindow instance — the accessor resolves the manager.
 *
 * @method static \ScrapyardIO\Tubes\Windows\PendingWindow driver(?string $driver = null)
 * @method static \ScrapyardIO\Tubes\Windows\PendingWindow make(?string $driver = null)
 * @method static \ScrapyardIO\Tubes\Windows\PendingWindow profile(string $name)
 * @method static \ScrapyardIO\Tubes\Windows\WindowManager extend(string $name, callable|string $creator)
 * @method static array listWindows()
 * @method static string defaultDriver()
 *
 * @see \ScrapyardIO\Tubes\Windows\WindowManager
 */
class Window extends MagicAlias
{
    protected static function getMagicAliasAccessor(): string
    {
        return 'window';
    }
}
