<?php

namespace ScrapyardIO\Tubes\Core\Workflows\WindowLoop;

use Fabricate\Sketches\Flow\Node;
use ScrapyardIO\Tubes\Canvas\OSWindow;
use ScrapyardIO\Tubes\Core\MagicAliases\Window;
use ScrapyardIO\Tubes\Windows\PendingWindow;
use ScrapyardIO\Tubes\Windows\WindowException;

/**
 * Open a registered window driver into shared['window'].
 *
 * Preferred: shared['profile'] → Window::profile(...).
 * Fallback: shared driver/title/width/height (legacy).
 *
 * Optional overrides on top of a profile: driver, title, width, height.
 */
class OpenWindowNode extends Node
{
    public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
    {
        try {
            $pending = $this->resolvePending($shared);
            /** @var OSWindow $window */
            $window = $pending->open();
        } catch (WindowException $e) {
            $shared['error'] = $e->getMessage();

            return 'fail';
        }

        $shared['window'] = $window;
        $shared['driver'] = $pending->driver();
        $shared['title'] = $pending->titleValue();
        $shared['width'] = $pending->widthValue();
        $shared['height'] = $pending->heightValue();
        $shared['tick'] = 0;

        return 'default';
    }

    /**
     * @param  array<string, mixed>  $shared
     */
    protected function resolvePending(array $shared): PendingWindow
    {
        $profile = is_string($shared['profile'] ?? null) ? trim($shared['profile']) : '';

        if ($profile !== '') {
            $pending = Window::profile($profile);

            $driver = is_string($shared['driver'] ?? null) ? strtolower(trim($shared['driver'])) : '';
            if ($driver !== '' && $driver !== $pending->driver()) {
                $pending = Window::driver($driver)
                    ->title($pending->titleValue())
                    ->size($pending->widthValue(), $pending->heightValue())
                    ->options($pending->optionsValue());
            }

            if (is_string($shared['title'] ?? null) && $shared['title'] !== '') {
                $pending->title($shared['title']);
            }

            if (is_int($shared['width'] ?? null) && $shared['width'] >= 1) {
                $pending->width($shared['width']);
            }

            if (is_int($shared['height'] ?? null) && $shared['height'] >= 1) {
                $pending->height($shared['height']);
            }

            return $pending;
        }

        $driver = is_string($shared['driver'] ?? null) ? $shared['driver'] : 'metal';
        $title = is_string($shared['title'] ?? null) ? $shared['title'] : 'Tubes Window';
        $width = is_int($shared['width'] ?? null) ? $shared['width'] : 800;
        $height = is_int($shared['height'] ?? null) ? $shared['height'] : 600;

        return Window::driver($driver)
            ->title($title)
            ->size($width, $height);
    }
}
