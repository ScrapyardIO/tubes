<?php

namespace ScrapyardIO\Tubes\Core\Console;

use Fabricate\Console\Command;
use ScrapyardIO\Tubes\Core\Enums\GfxCompanionTarget;
use ScrapyardIO\Tubes\Core\Workflows\GfxInstall\GfxUninstallFlow;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'uninstall:gfx')]
class UninstallGfxCommand extends Command
{
    protected ?string $signature = 'uninstall:gfx
                    {--composer=global : Absolute path to the Composer binary}
                    {--sdl3 : Remove SDL3 GFX stack}
                    {--metal : Remove Metal GFX stack}
                    {--open-gl : Remove OpenGL GFX stack}
                    {--vulkan : Remove Vulkan GFX stack}
                    {--cuda : Remove CUDA GFX stack}
                    {--dry-run : Show removals without running composer}';

    protected string $description = 'Remove microscrap GFX companions and published framebuffer config stubs';

    public function handle(): int
    {
        $shared = [
            'cli_targets' => $this->cliTargets(),
            'composer' => $this->option('composer'),
            'dry_run' => (bool) $this->option('dry-run'),
            'logs' => [],
        ];

        GfxUninstallFlow::make()->run($shared);

        foreach ($shared['logs'] ?? [] as $line) {
            if (is_string($line) && $line !== '') {
                $this->components->twoColumnDetail('gfx', $line);
            }
        }

        if (! empty($shared['failed'])) {
            $this->components->error($shared['error'] ?? 'GFX uninstall failed.');

            return self::FAILURE;
        }

        if (! empty($shared['cancelled']) || ($shared['targets'] ?? []) === []) {
            $this->components->warn('No GFX companions were selected for removal.');

            return self::SUCCESS;
        }

        $this->components->info('GFX uninstall finished.');

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    protected function cliTargets(): array
    {
        $selected = [];
        foreach (GfxCompanionTarget::cases() as $target) {
            if ($this->option($target->value)) {
                $selected[] = $target->value;
            }
        }

        return $selected;
    }
}
