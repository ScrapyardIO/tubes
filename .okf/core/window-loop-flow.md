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
| `profile` | Preferred — `Window::profile()` slug / dotted path |
| `driver` | Window slug (`metal`, `sdl3`, …); optional override when `profile` set |
| `title` / `width` / `height` | OSWindow open args / optional profile overrides |
| `paint` | `callable(OSWindow, int $tick): void` — hand-draw or future engine |
| `should_stop` / `runner` | Cooperative exit |
| `window` / `tick` | Runtime state |

# Evolution

Swap `paint` for a tubes rendering-engine tick without changing the graph. Package sketch: [CanvasWindowDemo](metal-canvas-sketch.md) — `MetalCanvasFlow` (AsyncFlow) runs `BallPhysicsNode` (AsyncNode / fiber) then `PaintTickNode`; paint draws via `Renderer2D` (`./runner canvas-window-demo [driver?] --profile=canvas-window-demo`).

# Related

- [Window factory](window-factory.md)
- [WindowHandler](window-handler.md)
- [Canvas](canvas.md)
