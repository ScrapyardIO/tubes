<?php

namespace ScrapyardIO\Tubes\Core\Workflows\WindowLoop;

use Fabricate\Sketches\Flow\Node;
use ScrapyardIO\Tubes\Canvas\PanelIC;
use ScrapyardIO\Tubes\Core\MagicAliases\Panel;
use ScrapyardIO\Tubes\Panels\PanelException;

/**
 * Open a registered panel profile into shared['canvas'].
 *
 * Preferred: shared['panel_profile'] → Panel::profile(...).
 * Fallback: shared['profile'] when it names a panels.* slug.
 *
 * If shared['canvas'] is already a PanelIC, this node is a no-op (sketch pre-open).
 */
class OpenPanelNode extends Node
{
    public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
    {
        $existing = $shared['canvas'] ?? null;
        if ($existing instanceof PanelIC) {
            $shared['width'] = $existing->width();
            $shared['height'] = $existing->height();
            $shared['tick'] = is_int($shared['tick'] ?? null) ? $shared['tick'] : 0;

            return 'default';
        }

        try {
            $profile = $this->resolveProfile($shared);
            $panel = Panel::profile($profile);
        } catch (PanelException $e) {
            $shared['error'] = $e->getMessage();

            return 'fail';
        }

        $shared['canvas'] = $panel;
        $shared['panel_profile'] = $profile;
        $shared['width'] = $panel->width();
        $shared['height'] = $panel->height();
        $shared['tick'] = 0;

        return 'default';
    }

    /**
     * @param  array<string, mixed>  $shared
     */
    protected function resolveProfile(array $shared): string
    {
        $panelProfile = is_string($shared['panel_profile'] ?? null)
            ? trim($shared['panel_profile'])
            : '';

        if ($panelProfile !== '') {
            return $panelProfile;
        }

        $profile = is_string($shared['profile'] ?? null) ? trim($shared['profile']) : '';
        if ($profile !== '') {
            return $profile;
        }

        $default = function_exists('config') ? config('tubes.defaults.canvas') : null;
        if (is_string($default) && trim($default) !== '') {
            return trim($default);
        }

        throw new PanelException(
            'OpenPanelNode requires shared[panel_profile], shared[profile], or tubes.defaults.canvas.'
        );
    }
}
