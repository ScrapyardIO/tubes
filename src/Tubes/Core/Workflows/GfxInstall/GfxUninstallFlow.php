<?php

namespace ScrapyardIO\Tubes\Core\Workflows\GfxInstall;

use Fabricate\Sketches\Flow\Flow;
use Fabricate\Sketches\Flow\Node;
use ScrapyardIO\Tubes\Core\Enums\GfxCompanionTarget;

use function Fabricate\Console\Prompts\disabled_multiselect;

/**
 * Prompt installed gfx targets → composer remove → delete published config stubs.
 */
class GfxUninstallFlow extends Flow
{
    public static function make(): self
    {
        $prompt = new class extends Node
        {
            public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
            {
                $cli = $shared['cli_targets'] ?? [];

                if (is_array($cli) && $cli !== []) {
                    $shared['targets'] = array_values(array_filter(array_map(
                        static fn (string $value): ?GfxCompanionTarget => GfxCompanionTarget::tryFrom($value),
                        $cli,
                    )));

                    return $shared['targets'] === [] ? 'empty' : 'default';
                }

                $options = [];
                foreach (GfxCompanionTarget::cases() as $target) {
                    if ($target->isHollow()) {
                        continue;
                    }
                    $options[$target->value] = $target->label();
                }

                $picked = disabled_multiselect(
                    label: 'Which GFX companions should be removed?',
                    options: $options,
                    hint: 'Removes Composer packages and published config/framebuffers/<slug>.php',
                );

                $shared['targets'] = array_values(array_filter(array_map(
                    static fn (string $value): ?GfxCompanionTarget => GfxCompanionTarget::tryFrom($value),
                    is_array($picked) ? $picked : [],
                )));

                return $shared['targets'] === [] ? 'empty' : 'default';
            }
        };

        $remove = new ComposerRemoveGfxNode;
        $empty = new class extends Node
        {
            public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
            {
                $shared['cancelled'] = true;

                return 'default';
            }
        };
        $fail = new class extends Node
        {
            public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
            {
                $shared['failed'] = true;

                return 'default';
            }
        };

        $prompt->next($remove);
        $prompt->on('empty')->next($empty);
        $remove->on('fail')->next($fail);

        return new self($prompt);
    }
}
