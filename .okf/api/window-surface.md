---
type: CoreType
title: WindowSurface
description: Contract and abstract base for a window handle
resource: /src/Tubes/Contracts/Windows/WindowSurface.php
tags: [tubes, api]
status: stable
generated: { by: cursor-agent/grok-4.6, at: "2026-08-17T04:30:00Z" }
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

`Tubes\Contracts\Windows\WindowSurface` extends `CanOwnMenuBars` and declares `close()`, `present()`, `isClosed()`, `getPointer()`, `getCurrentWidth()`, `getCurrentHeight()`, `getContentPointer()`, `setContentPointer()`, `addView(string $name, ViewType $view_component_enum, int $x, int $y, int $h, int $w, array $addl_params = [])`, `addLabel(…)`, `addButton(…)`, `addEntry(…)`, `addCheckbox(…)`, `pollClick(string $name): bool`, `pollResize(): bool`, `setRelayout(?callable $fn): static`, `setViewFrame(string $name, int $x, int $y, int $h, int $w): static`, `setLabelText(string $name, string $text): static`, `getEntryText(string $name): string`, `setEntryText(string $name, string $text): static`, `isCheckboxChecked(string $name): bool`, `setCheckboxChecked(string $name, bool $checked): static`, `showAlert(string $message, string $detail = '', array $buttons = ['OK']): static`, and `pollAlert(): ?int`.[^contract]

`ViewType` is `Tubes\Windows\Enums\ViewType` (`LABEL`, `BUTTON`, `ENTRY`, `CHECKBOX`, `SWITCH`). Parameter order is x, y, **h**, **w**. **Y origin is bottom-left, Y up** (AppKit). The GTK backend converts with `currentHeight - y - h` before `gtk_fixed_put`. `$addl_params['alignment']` is `Tubes\Windows\Enums\TextAlignment` (or its string value) for label text alignment. Optional `$addl_params['font_size']` (float, points) and `$addl_params['font_weight']` (`Tubes\Windows\Enums\FontWeight` or int 0–8) style labels; backends map to native font APIs. `addView(ViewType::LABEL, …)` delegates to `addLabel`. `addView(ViewType::BUTTON, …)` delegates to `addButton`. `addView(ViewType::ENTRY, …)` delegates to `addEntry`. `addView(ViewType::CHECKBOX, …)` delegates to `addCheckbox`.

# Alerts

`showAlert($message, $detail = '', $buttons = ['OK'])` opens one native alert on the window. Only one alert may be open; a second `showAlert` while one is pending throws `WindowableException`. `pollAlert(): ?int` drains the user response once per frame: `null` when nothing this frame; `0+` is the **0-based button index** (AppKit maps `NSAlertFirstButtonReturn` 1000 → 0). Sketches poll alerts before other input (e.g. `pollClick`).

# Resize

Tracked size is the **content area** (client bounds), not window chrome (title bar, menu bar). `$current_width` / `$current_height` are initialized from `$starting_width` / `$starting_height` at construct and exposed via `getCurrentWidth()` / `getCurrentHeight()`.

`pollResize(): bool` asks the backend for the native content size once per sketch frame (after `pump()`, same style as `pollClick` — not hidden inside `pump()`). It calls protected `nativeContentWidth()` / `nativeContentHeight()`. If either value is `<= 0`, the rock is left unchanged and the method returns `false` (GTK often reports `0` before the content widget is allocated). If width and height match the rock, returns `false`. Otherwise updates `$current_width` / `$current_height` and returns `true`. Sketches that receive `true` recompute layout with the same birth math used at create time and call `setViewFrame` on existing named views — no destroy/recreate, no Auto Layout.

`setRelayout(?callable $fn): static` stores a zero-arg callable the backend may invoke when the content size changes **during** a native resize, not only after `pump()` returns. AppKit live-resize runs a nested tracking loop inside `sendEvent`, so sketch-loop `pollResize` cannot see mid-drag frames; `AppKitWindowSurface` registers `ns_window_set_did_resize` and calls `pollResize()` then `$relayout`. GTK stores the callable only — `g_main_context_iteration` keeps running during stretch, so RunNode `pollResize` after `pump()` is enough. Pass `null` to clear.

`setViewFrame(string $name, int $x, int $y, int $h, int $w): static` moves and resizes an existing view. Parameter order matches `addView` (x, y, **h**, **w**). Missing `$name` throws `WindowableException` via `viewHandle`. Backends implement the native frame/size call.

# Base class

`Tubes\Windows\WindowSurface` implements the contract. Constructor arguments are `string $window_name`, `int $pointer`, `int $starting_width`, and `int $starting_height`; `$window_name` and `$pointer` are public readonly properties. `getPointer()` returns `$this->pointer`. `pollResize()` is concrete on the base; backends supply protected `nativeContentWidth()` / `nativeContentHeight()`. `setRelayout` stores `$relayout` on the base. `setViewFrame` is abstract on the base. `addView` and `addLabel` are abstract. `addButton`, `addEntry`, and `addCheckbox` are concrete and return `addView(ViewType::…, …)` — backends override them. `pollClick`, `setLabelText`, entry/checkbox get-set, and alert show/poll are on the contract; backends implement them. Duplicate view names throw `WindowableException`. Missing names on `viewHandle` throw `WindowableException`. `textAlignmentFrom($addl_params)` returns `TextAlignment` from `$addl_params['alignment']` or `null`. `fontSizeFrom($addl_params)` returns `float` from `$addl_params['font_size']` or `null`. `fontWeightFrom($addl_params)` returns `FontWeight` from `$addl_params['font_weight']` (enum or int) or `null`.[^base]

[^contract]: WindowSurface contract
[^base]: WindowSurface base
