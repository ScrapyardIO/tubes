<?php

namespace ScrapyardIO\Tubes\Core\MagicAliases;

use Fabricate\MagicAliases\MagicAlias;

/**
 * Magic alias for the tubes framebuffer factory.
 *
 * Never caches a Framebuffer instance — the accessor resolves the manager.
 *
 * @method static \ScrapyardIO\Tubes\Framebuffers\PendingFramebuffer driver(\ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\FramebufferDriver|string|null $driver = null)
 * @method static \ScrapyardIO\Tubes\Framebuffers\PendingFramebuffer managed(\ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\FramebufferDriver|string $driver)
 * @method static \ScrapyardIO\Tubes\Framebuffers\PendingFramebuffer deferred(string $driver)
 * @method static \ScrapyardIO\Tubes\Framebuffers\PendingFramebuffer make(?string $driver = null)
 * @method static \ScrapyardIO\Tubes\Framebuffers\PendingFramebuffer full()
 * @method static \ScrapyardIO\Tubes\Framebuffers\PendingFramebuffer dirty()
 * @method static \ScrapyardIO\Tubes\Framebuffers\PendingFramebuffer page()
 * @method static \ScrapyardIO\Tubes\Framebuffers\FramebufferManager extendManaged(string $name, callable|string $creator)
 * @method static \ScrapyardIO\Tubes\Framebuffers\FramebufferManager extendDeferred(string $name, callable|string $creator)
 * @method static array listFramebuffers(?\ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\FramebufferKind $kind = null)
 * @method static \ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\FramebufferKind|null kindOf(\ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\FramebufferDriver|string $driver)
 * @method static string defaultDriver()
 *
 * @see \ScrapyardIO\Tubes\Framebuffers\FramebufferManager
 */
class Framebuffer extends MagicAlias
{
    /**
     * Get the registered name of the component.
     */
    protected static function getMagicAliasAccessor(): string
    {
        return 'framebuffer';
    }
}
