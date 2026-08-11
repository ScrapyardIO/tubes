---
type: Core
title: "Canvas (OSWindow | PanelIC)"
description: Interchangeable presentation surface — consumers type-hint Canvas unless they need a concrete.
tags: [core, canvas, window, panel]
generated: { by: cursor-agent, at: "2026-08-09T04:10:00Z" }
status: draft
sources:
  - id: canvas
    resource: src/Tubes/Canvas/Canvas.php
    title: Abstract Canvas
  - id: oswindow
    resource: src/Tubes/Canvas/OSWindow.php
    title: OSWindow
  - id: panel
    resource: src/Tubes/Canvas/PanelIC.php
    title: PanelIC
---

# Role

`Canvas` is the abstract 2D presentation object. Sibling concretes `OSWindow` and `PanelIC` are **interchangeable** for nearly all consumers.

Type-hint `Canvas` unless you specifically need window polling, Circuit IC access, or panel factory pairing. If you need those, ask for `OSWindow` or `PanelIC` explicitly.

# Dump / present contract (Window or PanelIC)

If `framebuffer().hostFormat()` equals `canvas.formatSpec()`, **present must not rewrite the dump** — emit host bytes (full surface or dirty slices) unchanged. Conversion exists only when those FormatSpecs differ (e.g. engine Deferred host ≠ PanelIC FormatSpec). Soft CPU PanelIC is built so host **is** the IC FormatSpec.

# Partial refresh (CPU PanelIC)

When `device` implements `SupportsPartialRefresh` and the FB damage granularity is not whole-surface, `PanelIC::supportsPartialRefresh()` is true. Flush may emit `RenderType::PARTIAL` dumps; `transmit` windows by origin and ships `raw_data` intact. Sketches must avoid per-frame full `fill()` on that path.

# Shared consumer surface

| Method | Meaning |
|--------|---------|
| `width()` / `height()` | Logical size |
| `framebuffer()` | Pixel medium to borrow for drawing |
| `formatSpec()` | Sink layout facts |
| `present()` | Push pixels (native or IC transmit) |
| `close()` | Release the surface |

Typical paint (works for either concrete):

```php
function paint(Canvas $canvas, Renderer2D $renderer): void
{
    $fb = $canvas->framebuffer();
    $renderer->setFramebuffer($fb);
    // draw…
    $canvas->present();
}
```

# Concrete construction (not the consumer path)

| Concrete | How it is built | Present |
|----------|-----------------|---------|
| `OSWindow` | `WindowHandler` (in-engine Deferred) | Native |
| `PanelIC` | Chip + FB + Renderer2D (CPU Managed or engine headless) | `flush` → `transmit` |

`handler()`, `device()`, `renderer()`, `managedFramebuffer()` are **concrete escapes** — using them means you already chose a kind of canvas.

# Related

- [Panel factory](panel-factory.md)
- [WindowHandler](window-handler.md)
- [Output model](../orientation/output-model.md)
- [Draw / buffer / output ownership](../traps/draw-buffer-output.md)
- [Window ≠ IC Panel](../traps/window-vs-ic-panel.md)
