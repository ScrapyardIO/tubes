---
type: Core
title: CanvasWindowDemo sketch
description: Package-owned canvas-window-demo sketch — default PanelIC from tubes.defaults.canvas; window path via driver/--profile; AsyncNode ball physics.
tags: [core, sketch, canvas-window-demo, renderer2d, panel, human-input]
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
    title: defaults.canvas + window/panel canvas profiles
---

# Role

`ScrapyardIO\Tubes\Core\Runner\Sketches\CanvasWindowDemo` is the tubes demo sketch for Canvas + Renderer2D (+ Human Input on OSWindow).[^sketch]

- Attribute: `#[Sketch('canvas-window-demo')]` (Fabricate contracts)
- Extends `Fabricate\Sketches\Sketch` (not app convention base)
- Registered from `TubesServiceProvider::boot()` via `SketchRegistry::register()`[^provider]

## Default path (no CLI driver / --profile)

Resolves `tubes.defaults.canvas` via `CanvasProfiles::locate()` — any `windows.*` or `panels.*` profile.

- Panels → `Panel::profile(...)` / `MetalCanvasFlow::makePanel()`
- Windows → `Window::profile(...)` / `MetalCanvasFlow::make()`

Paint type-hints `Canvas`. Draw colours stay `0xRRGGBBAA`; `PixelStore` packs to the host FormatSpec on write (CPU host = IC).

CPU PanelIC with `SupportsPartialRefresh` + dirty/page FB: prime the surface once, then erase previous ball/HUD damage only (HUD ~10Hz) so flush emits PARTIAL. HUD FPS prefers `shared['work_ns']` (paint+present), not paced wall time. OSWindow path still full-clears each frame.

tubes-dev sets:

```php
'tubes.defaults' => [
    'framebuffer' => 'full',
    'font' => 'classic',
    'canvas' => 'st7796-front', // panels.st7796-front
];
'tubes.canvas_profiles.panels.st7796-front' => [
    'circuit' => 'st7796',
    'renderer' => PhpdafruitRenderer2D::class,
    'framebuffer' => 'dirty',
];
```

## Window path

Pass a window driver argument and/or `--profile=` under `canvas_profiles.windows` → `OpenWindowNode` / `Window::profile(...)`. Default window profile when forcing the window path without an explicit slug: `canvas-window-demo`. Click-boost Human Input applies only on OSWindow companions.

```bash
./runner canvas-window-demo                 # PanelIC default canvas
./runner canvas-window-demo metal           # OSWindow
./runner canvas-window-demo --profile=canvas-window-demo
```

`BallPhysicsNode` integrates with measured **delta time** (`shared['dt']`, seconds): velocity is px/s, boost accel is px/s². Bounds come from `shared['canvas']` (any `Canvas`). `FramePaceNode` sleeps to the target fps.

# Layout

| Path | Role |
|------|------|
| `Core/Runner/Sketches/CanvasWindowDemo.php` | Sketch entry + panel/window branch |
| `Core/Runner/Sketches/Support/` | HUD + click-boost helpers |
| `Core/Runner/Sketches/Workflows/` | MetalCanvasFlow + ball physics / frame pace |
| `Core/Workflows/WindowLoop/OpenPanelNode.php` | Panel profile open → `shared['canvas']` |
| Engine `*Renderer2D` / phpdafruit | Window engines / panel CPU renderer |

# Related

- [Tubes config](tubes-config.md)
- [Panel factory](panel-factory.md)
- [Window factory](window-factory.md)
- [Window loop Flow](window-loop-flow.md)
- [Rendering / Renderer2D](rendering.md)
- [Canvas](canvas.md)
- [SupportsPartialRefresh](supports-partial-refresh.md)

[^sketch]: CanvasWindowDemo source
[^provider]: TubesServiceProvider registration
