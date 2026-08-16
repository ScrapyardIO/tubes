---
type: CoreType
title: ViewType
description: Shared view catalog for WindowSurface::addView
resource: /src/Tubes/Windows/Enums/ViewType.php
tags: [tubes, api, enum]
status: draft
generated: { by: cursor-agent/grok-4.6, at: "2026-08-16T21:15:00Z" }
sources:
  - id: enum
    resource: /src/Tubes/Windows/Enums/ViewType.php
    title: ViewType.php
---

Backed `string` enum `Tubes\Windows\Enums\ViewType`. Cases: `LABEL`, `BUTTON`, `ENTRY`, `CHECKBOX`, `SWITCH`. `LABEL` also has `WindowSurface::addLabel` (`addView` with `ViewType::LABEL` calls it). `BUTTON` also has `WindowSurface::addButton` (`addView` with `ViewType::BUTTON` calls it).
