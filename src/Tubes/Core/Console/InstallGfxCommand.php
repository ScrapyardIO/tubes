<?php

namespace ScrapyardIO\Tubes\Core\Console;

use Fabricate\Console\Command;
use ScrapyardIO\Tubes\Core\Enums\GfxCompanionTarget;
use ScrapyardIO\Tubes\Core\Workflows\GfxInstall\GfxInstallFlow;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'install:gfx')]
class InstallGfxCommand extends Command
{
    protected ?string $signature = 'install:gfx
                    {--composer=global : Absolute path to the Composer binary}
                    {--force : Overwrite published framebuffer config stubs}
                    {--sdl3 : Install SDL3 GFX stack}
                    {--metal : Install Metal GFX stack}
                    {--open-gl : Install OpenGL GFX stack (microscrap/ogx)}
                    {--vulkan : Install Vulkan GFX stack}
                    {--cuda : Install CUDA GFX stack}
                    {--all : Install every allowed GFX companion}
                    {--dry-run : Resolve gates and packages without installing}';

    protected string $description = 'Install microscrap GFX companions for tubes framebuffers (OS/lib gated)';

    public function handle(): int
    {
        $shared = [
            'cli_targets' => $this->cliTargets(),
            'composer' => $this->option('composer'),
            'force' => (bool) $this->option('force'),
            'dry_run' => (bool) $this->option('dry-run'),
            'logs' => [],
        ];

        GfxInstallFlow::make()->run($shared);

        foreach ($shared['logs'] ?? [] as $line) {
            if (is_string($line) && $line !== '') {
                $this->components->twoColumnDetail('gfx', $line);
            }
        }

        if (! empty($shared['failed'])) {
            $this->components->error($shared['error'] ?? 'GFX install failed.');

            return self::FAILURE;
        }

        if (! empty($shared['cancelled']) || ($shared['targets'] ?? []) === []) {
            $this->components->warn('No GFX companions were selected.');

            return self::SUCCESS;
        }

        $labels = array_map(
            static fn (GfxCompanionTarget $t): string => $t->label(),
            $shared['targets'],
        );
        $this->components->info('GFX install finished: '.implode(', ', $labels));

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    protected function cliTargets(): array
    {
        if ($this->option('all')) {
            return array_map(
                static fn (GfxCompanionTarget $t): string => $t->value,
                array_filter(
                    GfxCompanionTarget::cases(),
                    static fn (GfxCompanionTarget $t): bool => ! $t->isHollow(),
                ),
            );
        }

        $selected = [];
        foreach (GfxCompanionTarget::cases() as $target) {
            $opt = $target->value;
            if ($this->option($opt)) {
                $selected[] = $target->value;
            }
        }

        return $selected;
    }
}
