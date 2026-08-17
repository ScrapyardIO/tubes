---
type: CoreType
title: FontWeight
description: Shared label font weight for WindowSurface::addLabel
resource: /src/Tubes/Windows/Enums/FontWeight.php
tags: [tubes, api, enum]
status: draft
generated: { by: cursor-agent/composer-2.5, at: "2026-08-17T01:22:00Z" }
sources:
  - id: enum
    resource: /src/Tubes/Windows/Enums/FontWeight.php
    title: FontWeight.php
---

Backed `int` enum `Tubes\Windows\Enums\FontWeight`. Cases: `ULTRA_LIGHT` (0) … `BLACK` (8), matching AppKit `FontWeightKind` / `NSFontWeight*` so backends map 1:1.

Pass as `$addl_params['font_weight']` to `WindowSurface::addLabel` (enum instance or int). Pair with optional `$addl_params['font_size']` (float, points). Backends apply native font styling; HelloForm never writes CSS or touches NSFont directly.
