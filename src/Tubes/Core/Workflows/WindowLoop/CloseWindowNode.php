<?php

namespace ScrapyardIO\Tubes\Core\Workflows\WindowLoop;

use Fabricate\Sketches\Flow\Node;
use ScrapyardIO\Tubes\Canvas\Canvas;

/**
 * Close shared['canvas'] / shared['window'] if present.
 */
class CloseWindowNode extends Node
{
    public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
    {
        $canvas = $shared['canvas'] ?? $shared['window'] ?? null;

        if ($canvas instanceof Canvas) {
            $canvas->close();
        }

        $shared['canvas'] = null;
        $shared['window'] = null;

        return 'default';
    }
}
