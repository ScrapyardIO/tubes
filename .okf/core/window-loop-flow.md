---
type: Core
title: Window loop Flow
description: WindowLoopFlow — open → paint/present/poll self-loop → close; paint callback is the evolution hook for rendering engines.
tags: [core, window, canvas, flow, sketch]
generated: { by: cursor-agent, at: "2026-08-09T04:30:00Z" }
status: draft
sources:
  - id: flow
    resource: src/Tubes/Core/Workflows/WindowLoop/WindowLoopFlow.php
    title: WindowLoopFlow
  - id: open
    resource: src/Tubes/Core/Workflows/WindowLoop/OpenWindowNode.php
    title: OpenWindowNode
  - id: tick
    resource: src/Tubes/Core/Workflows/WindowLoop/PaintTickNode.php
    title: PaintTickNode
  - id: close
    resource: src/Tubes/Core/Workflows/WindowLoop/CloseWindowNode.php
    title: CloseWindowNode
---

# Role

Reusable Fabricate `Flow` for a tubes Canvas present loop. Companions supply the `Window` driver; callers supply `paint`.

```text
OpenWindowNode → PaintTickNode ⇄ (continue) → CloseWindowNode (stop)
                 └ fail → CloseWindowNode
```

# Shared bag

| Key | Purpose |
|-----|---------|
| `profile` / `panel_profile` | Window or panel profile slug |
| `driver` | Window slug (`metal`, `sdl3`, …); optional override when window `profile` set |
| `title` / `width` / `height` | OSWindow open args / optional profile overrides; panel fills size from IC |
| `paint` | `callable(Canvas, int $tick): void` |
| `should_stop` / `runner` | Cooperative exit |
| `canvas` / `window` / `tick` | Runtime state (`canvas` preferred; `window` BC for OSWindow) |

`PaintTickNode` presents any `Canvas`; `pollEvents` / `shouldClose` only when the surface is an `OSWindow`. `OpenPanelNode` opens `Panel::profile(...)` into `shared['canvas']`. `CloseWindowNode` closes whichever Canvas is bound.

# Evolution

Swap `paint` for a tubes rendering-engine tick without changing the graph. Package sketch: [CanvasWindowDemo](metal-canvas-sketch.md) — `MetalCanvasFlow::make()` (window) or `makePanel()`; `BallPhysicsNode` + `PaintTickNode`; paint draws via `Renderer2D`.

# Related

- [Window factory](window-factory.md)
- [WindowHandler](window-handler.md)
- [Canvas](canvas.md)
