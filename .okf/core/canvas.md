---
type: Core
title: "Canvas (OSWindow | PanelIC)"
description: Abstract presentation surface — siblings OS Window (WindowHandler) and IC Panel (Managed buffer).
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

`Canvas` is the abstract 2D presentation object. Sibling concretes:

| Concrete | Pixel medium | Present |
|----------|--------------|---------|
| `OSWindow` | Companion `WindowHandler` → DeferredFramebuffer | Native (`presentNative`) |
| `PanelIC` | ManagedFramebuffer | IC transmit (stub / later) |

Draw code asks for `framebuffer()` and does not branch on Window vs Panel.

# OSWindow

Concrete tubes wrapper: `new OSWindow(WindowHandler $handler)`. Does **not** take a FormatSpec or Framebuffer from the caller — the handler owns both.

# Related

- [WindowHandler](window-handler.md)
- [Output model](../orientation/output-model.md)
- [Draw / buffer / output ownership](../traps/draw-buffer-output.md)
