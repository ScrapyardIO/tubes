---
type: CoreType
title: Config
description: config/windows.php keys used by the drivers
resource: /config/windows.php
tags: [tubes, api]
status: stable
generated: { by: cursor-agent/grok-4.6, at: "2026-08-16T13:39:00Z" }
verified:
    - { by: human:angel@projectsaturnstudios.com, at: "2026-08-16T13:46:00Z"}
sources:
  - id: config
    resource: /config/windows.php
    title: windows.php
---

# Keys

The file returns:[^config]

```php
[
    'mac' => [
        'app_name' => 'ScrapyardIO AppKit App',
    ],
    'linux' => [
        'application_id' => 'io.scrapyard.app',
        'application_flags' => 0,
    ],
]
```

`AppKitWindowDriver` reads `$config['app_name']`. `GTKWindowDriver` reads `$config['application_id']` and `$config['application_flags']`.

[^config]: windows.php
