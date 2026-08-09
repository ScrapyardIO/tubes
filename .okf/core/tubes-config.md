---
type: Core
title: Tubes config (defaults + canvas profiles)
description: Hydrated config/tubes.php — MagicAlias defaults, canvas_profiles windows/panels, tubes-config publish tag.
tags: [core, config, defaults, canvas-profiles, publish]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-09T19:50:00Z" }
status: draft
sources:
  - id: config
    resource: config/tubes.php
    title: Package tubes.php
  - id: profiles
    resource: src/Tubes/Core/Support/CanvasProfiles.php
    title: CanvasProfiles resolver
  - id: kind
    resource: src/Tubes/Core/Enums/CanvasProfileKind.php
    title: CanvasProfileKind enum
  - id: provider
    resource: src/Tubes/Core/Providers/TubesServiceProvider.php
    title: mergeConfigFrom + tubes-config publish
---

# Role

Package-owned `config/tubes.php` is the **hydrated root config** for tubes MagicAlias defaults and canvas presets. Merged as `tubes` from `TubesServiceProvider::register()`; published to the app as `config/tubes.php` via Workshop:

```bash
workshop vendor:publish --tag=tubes-config
```

# Defaults

```php
'tubes.defaults' => [
    'window' => env('WINDOW_DRIVER', 'sdl3'),
    'framebuffer' => env('FRAMEBUFFER_DRIVER', 'full'),
    'font' => env('FONT_DRIVER', 'classic'),
];
```

Used when `Window::driver()` / `Framebuffer::driver()` / `Font::driver()` (or `Font::font()`) are called **without** a name.

`TubesServiceProvider::boot()` syncs these into `windows.default`, `framebuffers.default`, and `fonts.default` so subsystem managers stay aligned, and surfaces them on Workshop `about` under **Drivers** (`Windows` / `Framebuffers` / `Fonts`).

# Canvas profiles

Segmented under `tubes.canvas_profiles`:

| Segment | Key | Instantiation |
|---------|-----|----------------|
| Windows | `canvas_profiles.windows.{slug}` | `Window::profile('slug')` or dotted path |
| Panels | `canvas_profiles.panels.{slug}` | Reserved until PanelIC factory lands |

## Window profile keys

| Key | Notes |
|-----|--------|
| `driver` | Required unless falling back to MagicAlias default |
| `title` | Optional string |
| `width` / `height` | Optional ints |
| `resolution` | Optional `[w,h]` or `"WxH"` when width/height omitted |
| `options` | Optional array merged into `PendingWindow` |
| *(other scalars)* | Copied into options |

```php
Window::profile('demo');
Window::profile('canvas-window-demo');
Window::profile('tubes.canvas_profiles.windows.metal-canvas'); // BC alias
```

Package presets ship `canvas-window-demo` / `metal-canvas` at 800×600. Apps (e.g. tubes-dev) override size/driver via published `config/tubes.php`.

Resolver: `CanvasProfiles` + string-backed enum `CanvasProfileKind` (`WINDOWS` / `PANELS`). Dotted paths must match the requested kind (`Window::profile('tubes.canvas_profiles.panels…')` is rejected).

# Related

- [Window factory](window-factory.md)
- [Framebuffer factory](framebuffer-factory.md)
- [Fonts / FontManager](fonts.md)
- [TubesServiceProvider](tubes-service-provider.md)
