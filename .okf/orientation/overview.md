---
type: Orientation
title: Package overview
description: Composer identity, autoload, and service wiring for scrapyard-io/tubes
resource: /composer.json
tags: [tubes, orientation]
status: stable
generated: { by: cursor-agent/grok-4.6, at: "2026-08-16T13:39:00Z" }
verified:
    - { by: human:angel@projectsaturnstudios.com, at: "2026-08-16T13:46:00Z"}
sources:
  - id: composer
    resource: /composer.json
    title: Package manifest
  - id: tubes-provider
    resource: /src/Tubes/Core/Providers/TubesServiceProvider.php
    title: TubesServiceProvider
  - id: windows-provider
    resource: /src/Tubes/Windows/OSWindowsServiceProvider.php
    title: OSWindowsServiceProvider
---

# Summary

`scrapyard-io/tubes` is a PHP library. Composer name is `scrapyard-io/tubes`, version `0.8.0`, license MIT. It requires PHP `^8.4|^8.5|^8.6`. PSR-4 maps `Tubes\` to `src/Tubes`. The `replace` keys are `tubes/contracts` and `tubes/windows` at `self.version`.[^composer]

`extra.scrapyard-io.providers` lists `Tubes\\Core\\Providers\\TubesServiceProvider`. That class extends `AggregateServiceProvider` and registers `OSWindowsServiceProvider`.[^tubes-provider]

`OSWindowsServiceProvider` merges `config/windows.php` as `windows`, binds `WindowManager` and `window` as singletons, and publishes the config file under tag `tubes-windows-config` when running in CLI and `configPath` exists on the container.[^windows-provider]

| Fact | Value |
|------|--------|
| Package | `scrapyard-io/tubes` |
| Version | `0.8.0` |
| PHP | `^8.4\|^8.5\|^8.6` |
| License | MIT |
| Autoload | `Tubes\` → `src/Tubes` |

[^composer]: Package manifest
[^tubes-provider]: TubesServiceProvider
[^windows-provider]: OSWindowsServiceProvider
