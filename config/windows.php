<?php

use Microscrap\Bindings\Gtk\Enums\ApplicationFlags;

return [
    'mac' => [
        'app_name' => 'ScrapyardIO AppKit App',
    ],
    'linux' => [
        'application_id' => 'io.scrapyard.app',
        'application_flags' => 0,//ApplicationFlags::DEFAULT_FLAGS->value,
    ],
];
