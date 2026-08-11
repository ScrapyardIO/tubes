---
type: Core
title: "SupportsPartialRefresh"
description: Contracts/Core marker for sinks that accept PARTIAL DumpedBuffer frames (address/page windows).
tags: [core, contracts, partial, panel]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-11T03:30:00Z" }
status: draft
sources:
  - id: iface
    resource: src/Tubes/Contracts/Core/SupportsPartialRefresh.php
    title: SupportsPartialRefresh
  - id: panelic
    resource: src/Tubes/Canvas/PanelIC.php
    title: PanelIC::supportsPartialRefresh
---

# Role

Marker interface: the sink can take `RenderType::PARTIAL` dumps without a mandatory full-surface rewrite.

Implemented by DOSR panel ICs: ST7789 / ST7735 / ST7796, GC9A01, SSD1306, SH1106.

`PanelIC::supportsPartialRefresh()` = device implements this **and** FB `damageGranularity()` is not whole-surface (dirty/page).

CPU paint must prime once then erase local damage — see [Panel factory](panel-factory.md).

`PartiallyRefreshable` under Contracts/Panels is a deprecated alias.

# Related

- [Panel factory](panel-factory.md)
- [Managed framebuffers](managed-framebuffers.md)
- [Canvas](canvas.md)
