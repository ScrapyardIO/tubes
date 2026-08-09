<?php

namespace ScrapyardIO\Tubes\Core\Workflows\GfxInstall;

use Fabricate\Filesystem\Filesystem;
use Fabricate\NutsAndBolts\Composer;
use Fabricate\Sketches\Flow\Node;
use ScrapyardIO\Tubes\Core\Enums\GfxCompanionTarget;

/**
 * Composer-require *-gfx companion packages (after PHP ext + binding wrappers).
 */
class ComposerRequireGfxNode extends Node
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
            foreach ($target->gfxPackages() as $constraint) {
                $packages[] = $constraint;
            }
        }

        $packages = array_values(array_unique($packages));
        $shared['composer_packages'] = $packages;

        if ($packages === []) {
            return 'default';
        }

        if ($dryRun) {
            $shared['logs'] = array_merge($shared['logs'] ?? [], [
                'Would composer require gfx: '.implode(', ', $packages),
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
            $shared['error'] = 'composer require failed for gfx: '.implode(', ', $packages);

            return 'fail';
        }

        $shared['logs'] = array_merge($shared['logs'] ?? [], [
            'Installed gfx: '.implode(', ', $packages),
        ]);

        return 'default';
    }
}
