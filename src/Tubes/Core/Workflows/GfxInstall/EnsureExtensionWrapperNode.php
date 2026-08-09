<?php

namespace ScrapyardIO\Tubes\Core\Workflows\GfxInstall;

use Fabricate\Filesystem\Filesystem;
use Fabricate\NutsAndBolts\Composer;
use Fabricate\Sketches\Flow\Node;
use ScrapyardIO\Tubes\Core\Enums\GfxCompanionTarget;

/**
 * Composer-require parent binding / extension-wrapper packages (microscrap/sdl3, metal, …).
 *
 * Runs after PHP extensions and before *-gfx packages.
 */
class EnsureExtensionWrapperNode extends Node
{
    public function exec(mixed $prepRes): mixed
    {
        return null;
    }

    /**
     * @param  array<string, mixed>  $shared
     */
    public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
    {
        /** @var list<GfxCompanionTarget> $targets */
        $targets = $shared['targets'] ?? [];
        $dryRun = (bool) ($shared['dry_run'] ?? false);
        $composerBinary = $shared['composer'] ?? null;
        $packages = [];

        foreach ($targets as $target) {
            foreach ($target->bindingPackages() as $constraint) {
                $packages[] = $constraint;
            }
        }

        $packages = array_values(array_unique($packages));
        $shared['binding_packages'] = $packages;

        if ($packages === []) {
            return 'default';
        }

        if ($dryRun) {
            $shared['logs'] = array_merge($shared['logs'] ?? [], [
                'Would composer require bindings: '.implode(', ', $packages),
            ]);

            return 'default';
        }

        $composer = new Composer(new Filesystem, base_path());
        $ok = $composer->requirePackages(
            $packages,
            false,
            null,
            is_string($composerBinary) && $composerBinary !== 'global' ? $composerBinary : null,
        );

        if (! $ok) {
            $shared['error'] = 'composer require failed for bindings: '.implode(', ', $packages);

            return 'fail';
        }

        $shared['logs'] = array_merge($shared['logs'] ?? [], [
            'Installed bindings: '.implode(', ', $packages),
        ]);

        return 'default';
    }
}
