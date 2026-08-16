---
type: Architecture
title: Window stack
description: Manager, drivers, application, and surface types in scrapyard-io/tubes
resource: /src/Tubes/Windows/WindowManager.php
tags: [tubes, architecture]
status: stable
generated: { by: cursor-agent/grok-4.6, at: "2026-08-16T13:39:00Z" }
verified:
    - { by: human:angel@projectsaturnstudios.com, at: "2026-08-16T13:46:00Z"}
sources:
  - id: manager
    resource: /src/Tubes/Windows/WindowManager.php
    title: WindowManager
  - id: os-driver
    resource: /src/Tubes/Windows/Drivers/OSWindowDriver.php
    title: OSWindowDriver
  - id: app-base
    resource: /src/Tubes/Windows/WindowableApplication.php
    title: WindowableApplication base
  - id: surface-base
    resource: /src/Tubes/Windows/WindowSurface.php
    title: WindowSurface base
---

# Layers

```text
OSWindow (MagicAlias → 'window')
        │
        ▼
WindowManager (Fabricate Manager)
        │  createMacDriver / createLinuxDriver
        ▼
OSWindowDriver
        │  application()
        ▼
WindowableApplication
        │  windows Collection
        ▼
WindowSurface (window_name, pointer)
```

# Source map

| Path | Role |
|------|------|
| `src/Tubes/Windows/WindowManager.php` | Extends `Manager`. `createMacDriver()` builds `AppKitWindowDriver` from `config('windows.mac', [])`. `createLinuxDriver()` builds `GTKWindowDriver` from `config('windows.linux', [])`. `app()` returns `$this->driver($driver)->application()`. `getDefaultDriver()` returns `'mac'` when `php_uname()` contains `Darwin`, otherwise `'linux'`.[^manager] |
| `src/Tubes/Windows/Drivers/OSWindowDriver.php` | Abstract driver holding `protected WindowableApplication $os_app`. Declares `application()`.[^os-driver] |
| `src/Tubes/Windows/WindowableApplication.php` | Abstract class implementing the contract. Holds `protected Collection $windows`.[^app-base] |
| `src/Tubes/Windows/WindowSurface.php` | Abstract class. Constructor sets `window_name` and `pointer`. `getPointer()` returns `pointer`.[^surface-base] |

[^manager]: WindowManager
[^os-driver]: OSWindowDriver
[^app-base]: WindowableApplication base
[^surface-base]: WindowSurface base
