<?php

namespace Tubes\Windows;

use Fabricate\MagicAliases\MagicAlias;

class OSWindow extends MagicAlias
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getMagicAliasAccessor(): string
    {
        return 'window';
    }
}
