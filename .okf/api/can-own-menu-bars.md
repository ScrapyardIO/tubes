---
type: CoreType
title: CanOwnMenuBars
description: Menu ownership flag and menuAddItem
resource: /src/Tubes/Contracts/Windows/CanOwnMenuBars.php
tags: [tubes, api]
status: stable
generated: { by: cursor-agent/grok-4.6, at: "2026-08-16T13:39:00Z" }
verified:
    - { by: human:angel@projectsaturnstudios.com, at: "2026-08-16T13:46:00Z"}
sources:
  - id: contract
    resource: /src/Tubes/Contracts/Windows/CanOwnMenuBars.php
    title: CanOwnMenuBars.php
---

# Schema

`Tubes\Contracts\Windows\CanOwnMenuBars` declares:[^contract]

| Method | Returns |
|--------|---------|
| `ownsMenuBar()` | `bool` |
| `menuAddItem(string $menuTitle, string $itemTitle, string $keyEquivalent, string $actionId)` | `static` |

`WindowableApplication` and `WindowSurface` both extend this interface.

[^contract]: CanOwnMenuBars.php
