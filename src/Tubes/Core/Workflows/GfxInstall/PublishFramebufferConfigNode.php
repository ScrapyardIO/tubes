<?php

namespace ScrapyardIO\Tubes\Core\Workflows\GfxInstall;

use Fabricate\Sketches\Flow\Node;
use ScrapyardIO\Tubes\Core\Enums\GfxCompanionTarget;
use Symfony\Component\Process\Process;

use function Fabricate\NutsAndBolts\Helpers\php_binary;

class PublishFramebufferConfigNode extends Node
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
        $force = (bool) ($shared['force'] ?? false);
        $logs = [];

        foreach ($targets as $target) {
            $provider = $target->serviceProvider();
            if (is_null($provider)) {
                $logs[] = "No service provider publish mapping for [{$target->value}].";

                continue;
            }

            $args = [
                php_binary(),
                'workshop',
                'vendor:publish',
                '--provider='.$provider,
                '--tag='.$target->publishTag(),
            ];

            if ($force) {
                $args[] = '--force';
            }

            if ($dryRun) {
                $logs[] = 'Would run: '.implode(' ', $args);

                continue;
            }

            $process = new Process($args, base_path());
            $process->setTimeout(120);
            $process->run();
            $logs[] = trim($process->getOutput().$process->getErrorOutput());

            if (! $process->isSuccessful()) {
                $shared['error'] = "vendor:publish failed for [{$target->value}].";
                $shared['logs'] = array_merge($shared['logs'] ?? [], $logs);

                return 'fail';
            }
        }

        $shared['logs'] = array_merge($shared['logs'] ?? [], $logs);

        return 'default';
    }
}
