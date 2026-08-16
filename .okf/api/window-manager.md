---
type: CoreType
title: WindowManager
description: Driver factory and app() accessor
resource: /src/Tubes/Windows/WindowManager.php
tags: [tubes, api]
status: stable
generated: { by: cursor-agent/grok-4.6, at: "2026-08-16T13:39:00Z" }
verified:
    - { by: human:angel@projectsaturnstudios.com, at: "2026-08-16T13:46:00Z"}
sources:
  - id: manager
    resource: /src/Tubes/Windows/WindowManager.php
    title: WindowManager.php
---

# Schema

| Method | Returns | Behavior |
|--------|---------|----------|
| `createMacDriver()` | `AppKitWindowDriver` | `new AppKitWindowDriver(config('windows.mac', []))` |
| `createLinuxDriver()` | `GTKWindowDriver` | `new GTKWindowDriver(config('windows.linux', []))` |
| `app(?string $driver = null)` | `WindowableApplication` | `$this->driver($driver)->application()` |
| `getDefaultDriver()` | `?string` | `'mac'` if `php_uname()` contains `Darwin`, else `'linux'` |

Class `Tubes\Windows\WindowManager` extends `Fabricate\NutsAndBolts\Manager`.[^manager]

[^manager]: WindowManager.php
