---
type: Orientation
title: "Output model (Window | IC Panel)"
description: Final abstract output resolves to sibling concretes OS Window or IC Panel; draw → framebuffer → present.
tags: [orientation, output, window, ic-panel, framebuffer, architecture]
generated: { by: okf-documentation-generator/cursor, at: "2026-08-08T21:45:00Z" }
status: draft
sources:
  - id: angel-arch
    resource: product-owner-architecture
    title: Angel architecture decisions for tubes 0.7 (session brief)
  - id: scaffold
    resource: src/Tubes/Canvas
    title: Canvas / OSWindow / PanelIC + Windows/WindowHandler
---

# Model

The final abstract **output** resolves to one of two sibling concretes:[^angel-arch]

| Concrete | Role |
|----------|------|
| **OS Window** | Host / desktop (or embedded window) presentation path |
| **IC Panel** | Integrated-circuit-driven panel presentation path |

These are **siblings** under the abstract output — **not** “a Window that can be an IC.”[^angel-arch]

**Consumers** type-hint `Canvas` and treat Window / PanelIC as interchangeable (`framebuffer` → draw → `present`). Be concrete (`OSWindow` / `PanelIC`) only when you need window events, Circuit IC access, or panel lane pairing.

On disk: `Canvas` (`OSWindow` | `PanelIC`) plus `Windows/WindowHandler` for OS drivers and `Panels/PanelManager` for IC wraps.[^scaffold]

# Intended pipeline

```text
draw logic  →  framebuffer  →  canvas present
                 ↑                    ↑
         Deferred (window)      WindowHandler.presentNative
         Managed|Deferred       PanelIC.present → IC.transmit
         (panel IC)
```

- **Framebuffer** is the shared medium between drawing and presentation.[^angel-arch]
- **OS Window:** engine-bound — companion `WindowHandler` owns that engine’s DeferredFramebuffer; present is native.
- **IC Panel:** owns device + FB + Renderer2D. CPU: Managed + companion CPU renderer (phpdafruit). Engine: **headless** Deferred + same engine Renderer2D. Present always `flush(IC FormatSpec)` → `transmit`. Tubes does not ship a CPU renderer.[^scaffold]
- The **renderer / draw logic** should not care whether the sink is an OS Window or an IC Panel.[^angel-arch]
- **Present** is the canvas / handler’s job after pixels land in the buffer.

# Sequencing (0.7 restore)

| Path | Notes |
|------|-------|
| Window / embedded | Landed first (gfx companions) |
| IC Panel | Wrap + PreferredManaged + `useFramebuffer` for engine buffers |

# Related

- [Window ≠ IC Panel](../traps/window-vs-ic-panel.md)
- [Draw / buffer / output ownership](../traps/draw-buffer-output.md)
- [Panel factory](../core/panel-factory.md)
- [FramebuffersServiceProvider](../core/framebuffers-service-provider.md)

[^angel-arch]: Angel architecture decisions for tubes 0.7 (session brief)
[^scaffold]: `Canvas`/`OSWindow`/`WindowHandler`/`PanelIC` + `PanelManager` on disk; SSD1306 / ST77xx implement panel contracts.
