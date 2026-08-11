---
type: Core
title: Tubes config (defaults + canvas profiles)
description: Hydrated config/tubes.php — framebuffer/font/canvas defaults, canvas_profiles windows/panels, tubes-config publish tag.
tags: [core, config, defaults, canvas-profiles, publish]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-10T23:00:00Z" }
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

Package-owned `config/tubes.php` is the **hydrated root config** for tubes defaults and canvas presets. Merged as `tubes` from `TubesServiceProvider::register()`; published to the app as `config/tubes.php` via Workshop:

```bash
workshop vendor:publish --tag=tubes-config
```

# Defaults

```php
'tubes.defaults' => [
    'framebuffer' => env('FRAMEBUFFER_DRIVER', 'full'),
    'font' => env('FONT_DRIVER', 'classic'),
    'canvas' => env('TUBES_CANVAS', 'canvas-window-demo'), // windows.* or panels.* slug
];
```

| Key | Meaning |
|-----|---------|
| `framebuffer` | MagicAlias driver when `Framebuffer::driver()` has no name (synced → `framebuffers.default`) |
| `font` | MagicAlias driver when `Font::driver()` / `Font::font()` has no name (synced → `fonts.default`) |
| `canvas` | Default **presentation profile** — must be a slug under `canvas_profiles.windows` **or** `canvas_profiles.panels` |

There is **no** `tubes.defaults.window`. Window *driver* fallback stays on `config('windows.default')`. Presentation default is always `tubes.defaults.canvas`.

`CanvasProfiles::locate($slug)` resolves which segment owns the name (throws if missing or ambiguous; disambiguate with `windows.*` / `panels.*`).

`TubesServiceProvider::boot()` syncs framebuffer/font into subsystem defaults and surfaces Workshop `about` **Drivers**: Canvas / Framebuffers / Fonts.

# Canvas profiles

Segmented under `tubes.canvas_profiles`:

| Segment | Key | Instantiation |
|---------|-----|----------------|
| Windows | `canvas_profiles.windows.{slug}` | `Window::profile('slug')` or dotted path |
| Panels | `canvas_profiles.panels.{slug}` | `Panel::profile('slug')` → `Circuit::profile(circuit)` → wrap |

## Panel profile keys

| Key | Notes |
|-----|--------|
| `circuit` | **Required** — `config/circuits.php` profile name |
| `renderer` | **Required** — class-string extending tubes `Renderer2D` (companion) |
| `framebuffer` | **Required for CPU** (`page` / `full` / `dirty`). **Omit for engine** (`ProvisionsHeadlessFramebuffer`). |

## Window profile keys

| Key | Notes |
|-----|--------|
| `driver` | Required unless falling back to `windows.default` |
| `title` | Optional string |
| `width` / `height` | Optional ints |
| `resolution` | Optional `[w,h]` or `"WxH"` when width/height omitted |
| `options` | Optional array merged into `PendingWindow` |
| *(other scalars)* | Copied into options |

# Related

- [Window factory](window-factory.md)
- [Panel factory](panel-factory.md)
- [Framebuffer factory](framebuffer-factory.md)
- [Fonts / FontManager](fonts.md)
- [TubesServiceProvider](tubes-service-provider.md)
