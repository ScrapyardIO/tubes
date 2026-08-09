<?php

namespace ScrapyardIO\Tubes\Core\Workflows\GfxInstall;

use Fabricate\Filesystem\Filesystem;
use Fabricate\NutsAndBolts\Composer;
use Fabricate\Sketches\Flow\Node;
use ScrapyardIO\Tubes\Core\Enums\GfxCompanionTarget;

class ComposerRemoveGfxNode extends Node
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
            foreach ($target->composerPackageNames() as $name) {
                $packages[] = $name;
            }
        }

        $packages = array_values(array_unique($packages));

        if ($packages === []) {
            return 'default';
        }

        if ($dryRun) {
            $shared['logs'] = array_merge($shared['logs'] ?? [], [
                'Would composer remove: '.implode(', ', $packages),
            ]);

            return 'default';
        }

        $composer = new Composer(new Filesystem, base_path());
        $ok = $composer->removePackages(
            $packages,
            false,
            null,
            is_string($composerBinary) && $composerBinary !== 'global' ? $composerBinary : null,
        );

        if (! $ok) {
            $shared['error'] = 'composer remove failed for: '.implode(', ', $packages);

            return 'fail';
        }

        foreach ($targets as $target) {
            $path = base_path('config/framebuffers/'.$target->framebufferSlug().'.php');
            if (is_file($path)) {
                @unlink($path);
            }
        }

        $shared['logs'] = array_merge($shared['logs'] ?? [], [
            'Removed: '.implode(', ', $packages),
        ]);

        return 'default';
    }
}
