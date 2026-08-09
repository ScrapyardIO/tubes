<?php

namespace ScrapyardIO\Tubes\Core\Workflows\GfxInstall;

use Fabricate\Sketches\Flow\Node;
use ScrapyardIO\Tubes\Core\Enums\GfxCompanionTarget;

use function Fabricate\Console\Prompts\disabled_multiselect;

class PromptGfxTargetsNode extends Node
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
        /** @var array{allowed: list<string>, denied: array<string, string>, installed: array<string, string>} $host */
        $host = $shared['host'] ?? [
            'allowed' => [],
            'denied' => [],
            'installed' => [],
        ];
        $cli = $shared['cli_targets'] ?? [];

        if (is_array($cli) && $cli !== []) {
            $selected = [];
            foreach ($cli as $value) {
                $target = GfxCompanionTarget::tryFrom((string) $value);
                if (is_null($target)) {
                    continue;
                }
                if (! in_array($target->value, $host['allowed'], true)) {
                    $reason = $host['installed'][$target->value]
                        ?? $host['denied'][$target->value]
                        ?? 'unknown';
                    $shared['error'] = "Target [{$target->value}] is not allowed: {$reason}";

                    return 'fail';
                }
                $selected[] = $target;
            }

            $shared['targets'] = $selected;

            return $selected === [] ? 'empty' : 'default';
        }

        $options = [];
        $disabled = [];

        foreach (GfxCompanionTarget::cases() as $target) {
            $value = $target->value;
            $base = $target->label();

            if (isset($host['installed'][$value])) {
                $options[$value] = "{$base} — already installed";
                $disabled[] = $value;

                continue;
            }

            if (isset($host['denied'][$value])) {
                $options[$value] = "{$base} — {$host['denied'][$value]}";
                $disabled[] = $value;

                continue;
            }

            $options[$value] = $base;
        }

        if ($options === []) {
            $shared['targets'] = [];

            return 'empty';
        }

        $picked = disabled_multiselect(
            label: 'Which GFX companions should be installed?',
            options: $options,
            disabled: $disabled,
            hint: 'Dimmed rows are incompatible or already installed (see label).',
        );

        $shared['targets'] = array_values(array_filter(array_map(
            static fn (string $value): ?GfxCompanionTarget => GfxCompanionTarget::tryFrom($value),
            is_array($picked) ? $picked : [],
        )));

        return $shared['targets'] === [] ? 'empty' : 'default';
    }
}
