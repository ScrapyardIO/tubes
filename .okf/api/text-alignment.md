---
type: CoreType
title: TextAlignment
description: Shared label text alignment for WindowSurface::addView
resource: /src/Tubes/Windows/Enums/TextAlignment.php
tags: [tubes, api, enum]
status: draft
generated: { by: cursor-agent/grok-4.6, at: "2026-08-16T20:20:00Z" }
sources:
  - id: enum
    resource: /src/Tubes/Windows/Enums/TextAlignment.php
    title: TextAlignment.php
---

Backed `string` enum `Tubes\Windows\Enums\TextAlignment`. Cases: `LEFT` (`left`), `CENTER` (`center`), `RIGHT` (`right`).

Pass as `$addl_params['alignment']` to `WindowSurface::addView` or `WindowSurface::addLabel` (enum instance or the string value). Backends map it; this enum is not AppKit `NSTextAlignment` ints. Current macOS AppKit ABI (`TARGET_ABI_USES_IOS_VALUES`) is left `0`, center `1`, right `2` — the historic macOS swap (center `2`, right `1`) is not what this SDK uses.
