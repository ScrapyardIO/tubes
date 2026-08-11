---
type: Core
title: "Rendering / Renderer2D"
description: 2D DrawingAPI + Renderer2D — gfx packages subclass and write into a borrowed framebuffer via set/unset.
tags: [core, rendering, drawing, renderer2d]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-09T05:10:00Z" }
status: draft
sources:
  - id: renderer2d
    resource: src/Tubes/Rendering/Renderer2D.php
    title: Renderer2D
  - id: renderer
    resource: src/Tubes/Rendering/Renderer.php
    title: Renderer (framebuffer bind)
  - id: drawing-api
    resource: src/Tubes/Contracts/Rendering/DrawingAPI.php
    title: DrawingAPI contract
  - id: packaging
    resource: src/Tubes/Rendering/composer.json
    title: tubes/rendering component package
---

# Role

Tubes owns the **2D drawing contract**. Microscrap `*-gfx` packages subclass `Renderer2D` and override draw methods. Presentation (Canvas / WindowHandler) owns the framebuffer; the renderer **borrows** it.

# Bind (no pixel copy)

| Method | Notes |
|--------|--------|
| `setFramebuffer(Framebuffer &$framebuffer)` | Bind by reference — same instance, no RAM copy |
| `unsetFramebuffer()` | Drop the bind |
| `framebuffer()` | Bound buffer; throws `RenderingException` if unset |
| `hasFramebuffer()` | Bound? |

# DrawingAPI

`Renderer2D` implements `Contracts\Rendering\DrawingAPI` (pixels, lines, rects, rounds, triangles, fill). Defaults throw `notImplemented` so empty companion stubs stay loadable until each gfx package fills them in.

Tubes ships **contracts** (`DrawingAPI` / abstract `Renderer2D` with `notImplemented` defaults) — **not** a product CPU renderer. `SoftRenderer2D` is an `@internal` test forwarder only; sketches and PanelIC require a real companion.

| Path | Renderer | Framebuffer |
|------|----------|-------------|
| OSWindow | Engine `*Renderer2D` | That engine’s window Deferred |
| PanelIC CPU | Companion CPU `Renderer2D` (phpdafruit) | Explicit Managed (mono: IC FormatSpec; full-color: B32 draw host); present → IC FormatSpec |
| PanelIC engine | Engine `Renderer2D` + `ProvisionsHeadlessFramebuffer` | Auto headless Deferred (engine FormatSpec); present → IC FormatSpec |

Engine PanelIC: `renderer($metal)` only — never `useFramebuffer`. CPU: Managed + phpdafruit — never engine Deferred.

Primary line names: `drawHorizontalLine` / `drawVerticalLine` (aliases `drawHLine` / `drawVLine`).

Text surface on `DrawingAPI`: `setFont` / `setCursor` / `setTextColor` / `print` / … Soft path: `Rendering\Concerns\DrawsText`. Gfx packages override; defaults throw `notImplemented`.

# Packaging

Subtree `tubes/rendering` (requires `tubes/contracts`); umbrella `replace` maps it at `self.version`.

# Related

- [Fonts / FontManager](fonts.md)
- [Canvas](canvas.md) — Pass 2 attaches Renderer2D (not wired yet)
- [Draw / buffer / output ownership](../traps/draw-buffer-output.md)
- [Component subtree packaging](../conventions/component-subtree-packaging.md)
- Ecosystem: [/ecosystem/scrapyard-io/tubes/0.7.x/rendering](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/rendering)
