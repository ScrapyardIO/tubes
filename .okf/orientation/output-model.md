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

On disk: `Canvas` (`OSWindow` | `PanelIC`) plus `Windows/WindowHandler` for companion OS drivers.[^scaffold]

# Intended pipeline

```text
draw logic  →  framebuffer  →  canvas present
                 ↑                    ↑
         Deferred (window)      WindowHandler.presentNative
         Managed (panel IC)     PanelIC.present (transmit)
```

- **Framebuffer** is the shared medium between drawing and presentation.[^angel-arch]
- **OS Window:** companion `WindowHandler` defines `FormatSpec` at construct and binds its package DeferredFramebuffer — present is native (no PHP flush remux).
- The **renderer / draw logic** should not care whether the sink is an OS Window or an IC Panel.[^angel-arch]
- **Present** is the canvas / handler’s job after pixels land in the buffer.

# Sequencing (0.7 restore)

| Path | Notes |
|------|-------|
| Window / embedded | Can come **first** while reconstituting |
| IC Panel | Depends on restoring IC / circuits functionality **later**[^angel-arch] |

# Related

- [Window ≠ IC Panel](../traps/window-vs-ic-panel.md)
- [Draw / buffer / output ownership](../traps/draw-buffer-output.md)
- [FramebuffersServiceProvider](../core/framebuffers-service-provider.md)

[^angel-arch]: Angel architecture decisions for tubes 0.7 (session brief)
[^scaffold]: `Canvas`/`OSWindow`/`WindowHandler`/`WindowManager` on disk; `PanelIC` still a stub; gfx handlers stub native boot until companions fill them.
