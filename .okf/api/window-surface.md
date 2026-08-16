---
type: CoreType
title: WindowSurface
description: Contract and abstract base for a window handle
resource: /src/Tubes/Contracts/Windows/WindowSurface.php
tags: [tubes, api]
status: stable
generated: { by: cursor-agent/grok-4.6, at: "2026-08-16T21:55:00Z" }
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

`Tubes\Contracts\Windows\WindowSurface` extends `CanOwnMenuBars` and declares `close()`, `isClosed()`, `getPointer()`, `getContentPointer()`, `setContentPointer()`, `addView(string $name, ViewType $view_component_enum, int $x, int $y, int $h, int $w, array $addl_params = [])`, `addLabel(string $name, int $x, int $y, int $h, int $w, array $addl_params = [])`, `addButton(string $name, int $x, int $y, int $h, int $w, array $addl_params = [])`, `pollClick(string $name): bool`, and `setLabelText(string $name, string $text): static`.[^contract]

`ViewType` is `Tubes\Windows\Enums\ViewType` (`LABEL`, `BUTTON`, `ENTRY`, `CHECKBOX`, `SWITCH`). Parameter order is x, y, **h**, **w**. **Y origin is bottom-left, Y up** (AppKit). The GTK backend converts with `currentHeight - y - h` before `gtk_fixed_put`. `$addl_params['alignment']` is `Tubes\Windows\Enums\TextAlignment` (or its string value) for label text alignment. `addView(ViewType::LABEL, …)` delegates to `addLabel`. `addView(ViewType::BUTTON, …)` delegates to `addButton`.

# Base class

`Tubes\Windows\WindowSurface` implements the contract. Constructor arguments are `string $window_name` and `int $pointer`, stored as public readonly properties. `getPointer()` returns `$this->pointer`. `addView` and `addLabel` are abstract. `addButton` is concrete and returns `addView(ViewType::BUTTON, …)` — backends override it. `pollClick` and `setLabelText` are on the contract; backends implement them. Duplicate view names throw `WindowableException`. Missing names on `viewHandle` throw `WindowableException`. `textAlignmentFrom($addl_params)` returns `TextAlignment` from `$addl_params['alignment']` or `null`.[^base]

[^contract]: WindowSurface contract
[^base]: WindowSurface base
