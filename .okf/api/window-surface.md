---
type: CoreType
title: WindowSurface
description: Contract and abstract base for a window handle
resource: /src/Tubes/Contracts/Windows/WindowSurface.php
tags: [tubes, api]
status: stable
generated: { by: cursor-agent/grok-4.6, at: "2026-08-16T13:39:00Z" }
verified:
    - { by: human:angel@projectsaturnstudios.com, at: "2026-08-16T13:46:00Z"}
sources:
  - id: contract
    resource: /src/Tubes/Contracts/Windows/WindowSurface.php
    title: WindowSurface contract
  - id: base
    resource: /src/Tubes/Windows/WindowSurface.php
    title: WindowSurface base
---

# Contract

`Tubes\Contracts\Windows\WindowSurface` extends `CanOwnMenuBars` and declares `close(): void`.[^contract]

# Base class

`Tubes\Windows\WindowSurface` implements the contract. Constructor arguments are `string $window_name` and `int $pointer`, stored as public readonly properties. `getPointer()` returns `$this->pointer`.[^base]

[^contract]: WindowSurface contract
[^base]: WindowSurface base
