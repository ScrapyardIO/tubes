# Directory Update Log

## 2026-08-16 (pollClick / setLabelText on contract)

* `pollClick($name): bool` and `setLabelText($name, $text): static` moved from abstract base declarations onto `Tubes\Contracts\Windows\WindowSurface`. Backends still implement. Abstract base no longer redeclares them.

## 2026-08-16 (Y origin)

* WindowSurface x/y are AppKit-style: origin bottom-left, Y up. GTK `GTKWindowSurface::contentY` maps `$y` to `currentHeight - y - h` on `gtk_fixed_put`.

## 2026-08-16 (addButton / pollClick / setLabelText)

* Contract `addButton($name, $x, $y, $h, $w, $addl_params = [])`. Abstract base implements it by delegating to `addView(ViewType::BUTTON, …)`; backends override. `addView(BUTTON)` delegates to `addButton`.
* `pollClick($name): bool` and `setLabelText($name, $text)` are abstract on the base class only, not the contract. Backends implement. `viewHandle($name)` looks up `$this->views` and throws if missing.

## 2026-08-16 (addLabel)

* `WindowSurface::addLabel($name, $x, $y, $h, $w, $addl_params = [])` on the contract and abstract base. Backends implement it. `addView(ViewType::LABEL, …)` delegates to `addLabel`.

## 2026-08-16 (NSTextAlignment ABI)

* AppKit backend must not treat Tubes `CENTER` as integer `2`. Current macOS `NSTextAlignment` is iOS-style: center `1`, right `2`.

## 2026-08-16 (TextAlignment)

* `Tubes\Windows\Enums\TextAlignment` (`LEFT` / `CENTER` / `RIGHT`) for `$addl_params['alignment']` on `addView`. `WindowSurface::textAlignmentFrom` reads the enum or string value.

## 2026-08-16 (addView)

* `WindowSurface::addView($name, ViewType, $x, $y, $h, $w, $addl_params = [])`. Enum `Tubes\Windows\Enums\ViewType`. Duplicate names throw.

## 2026-08-16

* **Initialization**: OKF v0.2 bundle for `scrapyard-io/tubes` from `composer.json`, `config/windows.php`, and PHP sources under `src/Tubes`.
