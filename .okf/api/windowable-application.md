---
type: CoreType
title: WindowableApplication
description: Contract and abstract base for OS applications
resource: /src/Tubes/Contracts/Windows/WindowableApplication.php
tags: [tubes, api]
status: stable
generated: { by: cursor-agent/grok-4.6, at: "2026-08-17T16:40:00Z" }
verified:
    - { by: human:angel@projectsaturnstudios.com, at: "2026-08-16T13:46:00Z"}
sources:
  - id: contract
    resource: /src/Tubes/Contracts/Windows/WindowableApplication.php
    title: WindowableApplication contract
  - id: base
    resource: /src/Tubes/Windows/WindowableApplication.php
    title: WindowableApplication base
---

# Contract

`Tubes\Contracts\Windows\WindowableApplication` extends `CanOwnMenuBars` and declares:[^contract]

| Method | Returns |
|--------|---------|
| `pump()` | `void` |
| `terminate()` | `void` |
| `createWindow(string $name, int $width, int $height, ?WindowSurface &$window = null)` | `static` |
| `closeWindow(string $name, ?WindowSurface &$window = null)` | `void` |

# Base class

`Tubes\Windows\WindowableApplication` implements the contract and holds `protected Collection $windows`. `closeWindow` is concrete on the base: it calls `close()` on the live surface (by name, or the passed handle), `offsetUnset($name)`, and nulls the out-param. A closed window's name is then free for `createWindow`. Backends inherit this; they do not special-case GTK vs AppKit here. `createWindow` still throws `windowAlreadyCreated` when the name belongs to a **live** window.[^base]

[^contract]: WindowableApplication contract
[^base]: WindowableApplication base
