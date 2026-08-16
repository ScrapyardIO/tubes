---
type: Convention
title: Service registration
description: How tubes registers WindowManager and config
resource: /src/Tubes/Windows/OSWindowsServiceProvider.php
tags: [tubes, conventions]
status: stable
generated: { by: cursor-agent/grok-4.6, at: "2026-08-16T13:39:00Z" }
verified:
    - { by: human:angel@projectsaturnstudios.com, at: "2026-08-16T13:46:00Z"}
sources:
  - id: tubes-provider
    resource: /src/Tubes/Core/Providers/TubesServiceProvider.php
    title: TubesServiceProvider
  - id: windows-provider
    resource: /src/Tubes/Windows/OSWindowsServiceProvider.php
    title: OSWindowsServiceProvider
---

# Providers

`TubesServiceProvider` is an `AggregateServiceProvider` whose `$providers` array is `[OSWindowsServiceProvider::class]`.[^tubes-provider]

`OSWindowsServiceProvider::register()`:[^windows-provider]

1. `mergeConfigFrom(..., 'windows')` using `config/windows.php`
2. Singleton `WindowManager::class`
3. Singleton `'window'` resolving to `WindowManager`

`boot()` publishes that same config file to `$this->container->configPath('windows.php')` under tag `tubes-windows-config` when `PHP_SAPI` is `cli` or `phpdbg` and the container has `configPath`.

[^tubes-provider]: TubesServiceProvider
[^windows-provider]: OSWindowsServiceProvider
