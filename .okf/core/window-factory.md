---
type: Core
title: Window factory
description: WindowManager + config registry + make(); extend(handler); MagicAlias Window.
tags: [core, window, factory, magic-alias, canvas, config]
generated: { by: cursor-agent, at: "2026-08-09T04:20:00Z" }
status: draft
sources:
  - id: manager
    resource: src/Tubes/Windows/WindowManager.php
    title: WindowManager
  - id: pending
    resource: src/Tubes/Windows/PendingWindow.php
    title: PendingWindow
  - id: config
    resource: src/Tubes/Windows/config/windows.php
    title: windows config stub
  - id: alias
    resource: src/Tubes/Core/MagicAliases/Window.php
    title: Window MagicAlias
  - id: provider
    resource: src/Tubes/Windows/Providers/WindowsServiceProvider.php
    title: WindowsServiceProvider
---

# Role

Factory for **registered** OS window handlers. Companions register `WindowHandler` class-strings (or callables) and/or publish `config/windows/<slug>.php`. `create()` returns tubes concrete `OSWindow`; `open()` creates then opens natively.

# Fluent build

```php
Window::driver('sdl3')->title('Demo')->size(800, 600)->create();
Window::driver()->title('Demo')->size(800, 600)->create(); // tubes.defaults.window
Window::driver('sdl3')->title('Demo')->size(800, 600)->open();
Window::make()->title('Demo')->size(800, 600)->create();
Window::profile('demo'); // tubes.canvas_profiles.windows.demo
Window::profile('canvas-window-demo');
Window::profile('tubes.canvas_profiles.windows.metal-canvas'); // BC alias
```

`driver(?string $driver = null)` / `make(?string $driver = null)` use `config('tubes.defaults.window')` (synced to `windows.default` / `WINDOW_DRIVER`) when omitted (default slug `sdl3`).

`profile(string $name)` hydrates a `PendingWindow` from [tubes canvas profiles](tubes-config.md) (short slug or dotted config path).

# Config

- Root [tubes config](tubes-config.md) (`tubes-config` publish tag) owns defaults + `canvas_profiles.windows.*`.
- Package stub merged as `windows` (`tubes-windows-config` publish tag).
- App `config/windows/*.php` slug files merge into the driver registry (`kind`, `class`, `extension`).
- `create()` fails fast if `extension` is not loaded or `class` is missing.

# Registration

| Method | Purpose |
|--------|---------|
| `extend($name, callable\|class-string)` | Register handler creator |

Class-strings must extend `WindowHandler` with ctor `(string $title, int $width, int $height)`.

Companion slugs match framebuffer keys: `sdl3`, `open-gl`, `metal`, `vulkan`, `cuda`.

`install:gfx` surfaces `sdl3` / `open-gl` / `metal` / `vulkan` today; `cuda` registers when the package is path-/manually required (Workshop still treats CUDA as hollow).

# Related

- [Tubes config](tubes-config.md)
- [WindowHandler](window-handler.md)
- [Canvas / OSWindow](canvas.md)
- [Framebuffer factory](framebuffer-factory.md) — parallel registry pattern
