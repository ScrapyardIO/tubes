<?php

namespace ScrapyardIO\Tubes\Core\Workflows\GfxInstall;

use Fabricate\Sketches\Flow\Node;
use ScrapyardIO\Tubes\Core\Enums\GfxCompanionTarget;
use Symfony\Component\Process\Process;

/**
 * Ensure each selected target's PHP extension is loaded (PIE install when missing).
 *
 * Separate from binding wrappers and *-gfx Composer requires.
 */
class EnsurePhpExtensionNode extends Node
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
        $logs = [];

        foreach ($targets as $target) {
            $ext = $target->phpExtension();
            if (is_null($ext)) {
                continue;
            }

            if (extension_loaded($ext)) {
                $logs[] = "PHP extension [{$ext}] already loaded.";

                continue;
            }

            $pie = $target->piePackage();
            if (is_null($pie)) {
                $shared['error'] = "PHP extension [{$ext}] is required for {$target->label()} but no PIE package is mapped.";
                $shared['logs'] = array_merge($shared['logs'] ?? [], $logs);

                return 'fail';
            }

            if ($dryRun) {
                $logs[] = "Would PIE install [{$pie}] for ext-{$ext}.";

                continue;
            }

            if (! $this->pieInstall($pie, $logs)) {
                $shared['error'] = "Failed to install PHP extension via PIE [{$pie}].";
                $shared['logs'] = array_merge($shared['logs'] ?? [], $logs);

                return 'fail';
            }
        }

        $shared['logs'] = array_merge($shared['logs'] ?? [], $logs);

        return 'default';
    }

    /**
     * @param  list<string>  $logs
     */
    protected function pieInstall(string $package, array &$logs): bool
    {
        if (! $this->commandExists('pie')) {
            $logs[] = 'PIE binary not found; install php/pie PHAR then re-run.';

            return false;
        }

        $process = new Process(['pie', 'install', $package]);
        $process->setTimeout(600);
        $process->run();
        $logs[] = trim($process->getOutput().$process->getErrorOutput());

        return $process->isSuccessful();
    }

    protected function commandExists(string $binary): bool
    {
        $process = Process::fromShellCommandline('command -v '.escapeshellarg($binary));
        $process->run();

        return $process->isSuccessful();
    }
}
