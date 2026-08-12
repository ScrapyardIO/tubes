---
type: Orientation
title: Package (0.7)
description: scrapyard-io/tubes 0.7.0 — ScrapyardIO display companion; reconstituting with PixelStore, managed framebuffers, and Framebuffer factory.
resource: .
tags: [orientation, tubes, scrapyard-io, 0.7]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-08T23:05:00Z" }
status: draft
sources:
  - id: composer
    resource: composer.json
    title: Package name, version, PHP, autoload, scrapyard-io providers/aliases, replaces
  - id: tubes-sp
    resource: src/Tubes/Core/Providers/TubesServiceProvider.php
    title: Aggregate TubesServiceProvider
  - id: fb-sp
    resource: src/Tubes/Framebuffers/Providers/FramebuffersServiceProvider.php
    title: FramebuffersServiceProvider
---

# What it is

Composer package `scrapyard-io/tubes` at **0.7.0** — described as “The ScrapyardIO Display Panel Framework.”[^composer] It is an **opt-in companion** to `scrapyard-io/framework` 0.7 for display surfaces (see [ownership vs framework](ownership-vs-framework.md)).

Root [`README.md`](../README.md) is a short Packagist/GitHub landing page (Illuminate-style) with CI/Packagist/Docs badges that points to the [ecosystem docs](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/overview) — not a second docs tree. GitHub Actions: [`.github/workflows/tests.yml`](../.github/workflows/tests.yml) (PHP 8.4/8.5 · Pest). Full page inventory: [Ecosystem docs](ecosystem-docs.md).

| Field | Value |
|-------|-------|
| Name | `scrapyard-io/tubes` |
| Version | `0.7.0` |
| PHP | `^8.4\|^8.5\|^8.6`[^composer] |
| Namespace | `ScrapyardIO\Tubes\` → `src/Tubes`[^composer] |
| Discovery | `extra.scrapyard-io.providers` + `aliases` (`Framebuffer`)[^composer] |
| Role | Display companion (Window \| IC Panel output model) |

Composer `replace` maps split components (`tubes/canvas`, `tubes/contracts`, `tubes/fonts`, `tubes/framebuffers`, `tubes/human-input`, `tubes/inputs`, `tubes/panels`, `tubes/rendering`, `tubes/windows`) plus legacy 0.6 names (`tubes/monochrome`, `tubes/color`, `tubes/matrix`, `tubes/epaper`) to `self.version`. **No** `tubes/core` — Core stays umbrella-only.[^composer]

## Composer requires (granular)

Never kitchen-sink-require `scrapyard-io/framework` / `scrapyard-io/gpio-framework`. Mirror `scrapyard-io/waveforms`:

| Package | Why |
|---------|-----|
| `fabricate/nuts-and-bolts` | `AggregateServiceProvider` / `ServiceProvider` / `Composer` / `Splices4Bits` |
| `fabricate/magic-aliases` | Tubes MagicAliases |
| `fabricate/contracts` | `SketchRegistry` / sketch attributes |
| `fabricate/sketches` | `Sketch` / `Flow` / WindowLoop + GfxInstall nodes |
| `fabricate/console` | `Command` / `GeneratorCommand` |
| `fabricate/filesystem` | GfxInstall composer/path nodes |
| `gpio/contracts` | `IntegratedCircuit` on `PanelDevice` |
| `waveforms/contracts` | CircuitInput ButtonPad / GameController mapping |

Non-core components must not import Core (own or foreign). See [dependency direction](../conventions/dependency-direction.md).

# Reconstituting surface (on disk)

This checkout is **reconstituting** for ScrapyardIO 0.7. Document only what exists:

| Path | State |
|------|-------|
| `src/Tubes/Core/Providers/TubesServiceProvider.php` | Aggregate registering Framebuffers + Windows[^tubes-sp] |
| `src/Tubes/Core/MagicAliases/Framebuffer.php` | MagicAlias → `framebuffer` manager |
| `src/Tubes/Core/MagicAliases/Window.php` | MagicAlias → `window` manager |
| `src/Tubes/Framebuffers/` | PixelStore, Managed concretes, Manager, Pending, Provider[^fb-sp] |
| `src/Tubes/Windows/` | WindowHandler, WindowManager, PendingWindow, Provider |
| `src/Tubes/Canvas/` | Canvas, OSWindow |
| `src/Tubes/Rendering/` | Renderer, Renderer2D (DrawingAPI) |
| `src/Tubes/Contracts/Framebuffers/` | Contracts + FormatSpec / enums |
| `src/Tubes/Contracts/Rendering/` | DrawingAPI + RenderingException |
| `src/Tubes/Contracts/Windows/` | WindowFactory contract |
| `tests/Framebuffers/`, `tests/Windows/`, `tests/Rendering/` | Pest factory + handler + Renderer2D coverage |

Do **not** claim 0.6 classes (for example MonochromePanel / ColorPanel) exist in this tree unless PHP files are present.

# What it is not

- Not part of slim `scrapyard-io/framework` 0.7 core.
- Not a Fabricate domain under `Fabricate\*`.
- Not a finished display stack yet — framebuffer + window factories are present; PanelIC / native gfx wiring still ahead.

# Related

| Topic | Concept |
|-------|---------|
| Architecture | [Output model](output-model.md) |
| Factories | [Framebuffer factory](../core/framebuffer-factory.md), [Window factory](../core/window-factory.md) |
| Providers | [TubesServiceProvider](../core/tubes-service-provider.md) |
| Local develop | [Path-require from tubes-dev](../playbooks/path-require-tubes-dev.md) |

[^composer]: Package name, version, PHP, autoload, scrapyard-io providers/aliases, replaces
[^tubes-sp]: Aggregate TubesServiceProvider
[^fb-sp]: FramebuffersServiceProvider
