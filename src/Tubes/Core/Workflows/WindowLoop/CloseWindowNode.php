<?php

namespace ScrapyardIO\Tubes\Core\Workflows\WindowLoop;

use Fabricate\Sketches\Flow\Node;
use ScrapyardIO\Tubes\Canvas\OSWindow;

/**
 * Close shared['window'] if present.
 */
class CloseWindowNode extends Node
{
    public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
    {
        $window = $shared['window'] ?? null;

        if ($window instanceof OSWindow) {
            $window->close();
        }

        $shared['window'] = null;

        return 'default';
    }
}
