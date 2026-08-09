---
type: Core
title: CanvasWindowDemo sketch
description: Package-owned canvas-window-demo sketch — GFX soft Renderer2D, AsyncNode ball physics, OSWindow mouse click boost.
tags: [core, sketch, canvas-window-demo, renderer2d, human-input]
status: draft
sources:
  - id: sketch
    resource: src/Tubes/Core/Runner/Sketches/CanvasWindowDemo.php
    title: CanvasWindowDemo #[Sketch('canvas-window-demo')]
  - id: provider
    resource: src/Tubes/Core/Providers/TubesServiceProvider.php
    title: SketchRegistry registration
  - id: profile
    resource: config/tubes.php
    title: canvas-window-demo window profile preset
---

# Role

`ScrapyardIO\Tubes\Core\Runner\Sketches\CanvasWindowDemo` is the tubes demo sketch for Window + Renderer2D + Human Input.[^sketch]

- Attribute: `#[Sketch('canvas-window-demo')]` (Fabricate contracts)
- Extends `Fabricate\Sketches\Sketch` (not app convention base)
- Registered from `TubesServiceProvider::boot()` via `SketchRegistry::register()`[^provider]

Opens via `shared['profile']` → `OpenWindowNode` → `Window::profile(...)`. Default profile slug matches the sketch:

```php
Window::profile('canvas-window-demo'); // tubes.canvas_profiles.windows.canvas-window-demo
// BC alias: metal-canvas
```

CLI `--profile`, optional driver arg, and `--title`/`-W`/`-H` override the profile. Package default size is 800×600; tubes-dev app config uses **1024×768** metal.

# Layout

| Path | Role |
|------|------|
| `Core/Runner/Sketches/CanvasWindowDemo.php` | Sketch entry + soft GFX renderer resolve |
| `Core/Runner/Sketches/Support/` | HUD + click-boost helpers |
| `Core/Runner/Sketches/Workflows/` | Flow + ball physics / frame pace nodes |
| `Rendering/SoftRenderer2D.php` | CPU fallback when companion Renderer2D missing |

# Soft GFX

Companions are **not** tubes Composer requires. `resolveRenderer()` uses string FQCNs + `class_exists` for Metal / OGX / Vulkan / CUDA / SDL3 Renderer2D classes; otherwise `SoftRenderer2D`.

# Run

```bash
./runner canvas-window-demo
```

# Related

- [Tubes config](tubes-config.md)
- [Window factory](window-factory.md)
- [Rendering / Renderer2D](rendering.md)
- [HumanInput](human-input.md)

[^sketch]: CanvasWindowDemo source
[^provider]: TubesServiceProvider registration
