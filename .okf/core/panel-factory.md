---
type: Core
title: Panel factory (PanelIC wrap)
description: CPU = Managed FB + CPU renderer; Engine = renderer only (headless Deferred provisioned); no cross-wire.
tags: [core, panel, factory, circuit, wrap]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-10T22:30:00Z" }
status: draft
sources:
  - id: manager
    resource: src/Tubes/Panels/PanelManager.php
    title: PanelManager
  - id: lane
    resource: src/Tubes/Panels/Support/PanelLane.php
    title: PanelLane pairing
  - id: headless
    resource: src/Tubes/Contracts/Rendering/ProvisionsHeadlessFramebuffer.php
    title: ProvisionsHeadlessFramebuffer
---

# Pairing rules (enforced)

| Lane | Factory shape | Framebuffer | Renderer | Host FormatSpec |
|------|---------------|-------------|----------|-----------------|
| **CPU mono** | Managed **page** + CPU renderer | `PageSegmentBuffer` only | phpdafruit (etc.) | IC `FormatSpec` |
| **CPU color** | Managed **non-page** (`dirty` / `full`, …) + CPU renderer | Never page | phpdafruit (etc.) | Host **= IC FormatSpec** (no present transcode); RGBA packs on write |
| **Engine** | `renderer($engine)` **only** | Headless Deferred from `provisionHeadlessFramebuffer()` | Engine `Renderer2D` + `ProvisionsHeadlessFramebuffer` | Engine native; **present** → IC FormatSpec |

```php
// CPU mono — page required
Panel::make()->wrap($oled)->framebuffer('page')->renderer($phpdafruit)->create();

// CPU color — page forbidden
Panel::make()->wrap($tft)->framebuffer('full')->renderer($phpdafruit)->create();

// Engine
Panel::make()->wrap($ic)->renderer($metalRenderer)->create();
```

Illegal (throws): Managed + engine renderer; injected Deferred; CPU without Managed; engine + managed driver; mono + non-page; color + page.

# Present / FormatSpec

`PanelIC::present()` always `flush($device->formatSpec())` → `transmit` (binary string / `DumpedBuffer[]`).

**Canvas-wide rule** (same for Window): FB host FormatSpec == Canvas FormatSpec ⇒ dump unchanged — no flush remux.

- CPU full-color / mono: host **is** the IC FormatSpec — flush is dump / dirty memcpy. Sketches draw `0xRRGGBBAA`; `PixelStore` packs to host on write (solid fills pack once and stamp bytes).
- Engine: draw in GPU host FormatSpec; flush may convert only when host ≠ IC.
- Never per-sketch bit-pack.

# Partial refresh (CPU)

`SupportsPartialRefresh` (`Contracts\Core`) marks ICs that accept PARTIAL dumps (ST77xx, GC9A01, SSD1306, SH1106).

`PanelIC::supportsPartialRefresh()` is true when the device implements that marker **and** the bound FB damage unit is smaller than the whole surface (dirty / page — not `FullFramebuffer`).

CPU sketches must **not** `fill()` every frame on that path: prime once, then erase previous damage only so flush emits PARTIAL windows that `transmit()` streams via address/page registers.

# Partial refresh (Engine / SDL3)

Engine host FormatSpec ≠ IC FormatSpec → flush **transcodes** (e.g. SDL RGBA8888 → ST7789 RGB565). `Sdl3Framebuffer` (headless) tracks dirty rects and emits PARTIAL dumps when damage is sparse; UX Scene skips full clear once primed (`damageGranularity` pixel).

# Related

- [Canvas](canvas.md)
- [Rendering / Renderer2D](rendering.md)
- [Deferred framebuffer](deferred-framebuffer.md)
- [Managed framebuffers](managed-framebuffers.md)
- [SupportsPartialRefresh](supports-partial-refresh.md)
