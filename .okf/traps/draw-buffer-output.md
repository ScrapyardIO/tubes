---
type: Trap
title: Draw / buffer / output ownership
description: Draw writes the framebuffer; output presents it — renderer must not branch on Window vs IC Panel.
tags: [trap, framebuffer, renderer, present, architecture]
generated: { by: okf-documentation-generator/cursor, at: "2026-08-08T21:45:00Z" }
status: draft
sources:
  - id: angel-arch
    resource: product-owner-architecture
    title: Angel architecture decisions for tubes 0.7 (session brief)
---

# Trap

Letting draw / renderer code know (or branch on) whether the eventual sink is an OS Window or an IC Panel — or putting present/flush ownership on the wrong layer.[^angel-arch]

# Intended ownership

| Layer | Owns |
|-------|------|
| Draw logic (`Renderer2D`) | Writing pixels / primitives into a **borrowed** buffer (`setFramebuffer` / `unsetFramebuffer` by reference) |
| Framebuffer | Shared pixel medium (owned by Canvas / handler) |
| Abstract output | Presenting / transmitting the buffer to Window or IC Panel |

# Pairing rules

| Canvas | Framebuffer + renderer |
|--------|------------------------|
| `OSWindow` | In-engine via WindowHandler — that engine’s Deferred FB + matching Renderer2D |
| `PanelIC` | CPU: explicit Managed + CPU renderer. Engine: renderer only (`ProvisionsHeadlessFramebuffer` → headless Deferred). Cross-wire forbidden. Present always emits IC FormatSpec. |

# Interchangeability

Consumers type-hint `Canvas`. They borrow `framebuffer()`, draw with a `Renderer2D`, call `present()`. They do **not** branch on Window vs PanelIC. Construction/pairing differences stay at the factory that *creates* the canvas.

# Smell tests

- Renderer or paint helper imports / `instanceof` Window or PanelIC → wrong (be specific only when you need that concrete).
- Renderer allocates / copies a second pixel store instead of borrowing → wrong (RAM waste).
- “Bind display surface” shortcuts that skip the buffer for one sink only → wrong.
- Overloaded `flush` that means both “buffer finalize” and “device transmit” without clear boundaries → revisit.
- Forcing Managed-only on PanelIC when an engine Deferred is the intended medium → wrong.

# Related

- [Output model](../orientation/output-model.md)
- [Window ≠ IC Panel](window-vs-ic-panel.md)
- [FramebuffersServiceProvider](../core/framebuffers-service-provider.md)

[^angel-arch]: Angel architecture decisions for tubes 0.7 (session brief)
