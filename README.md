# scrapyard-io/tubes (0.7)

[![Tests](https://github.com/scrapyard-io/tubes/actions/workflows/tests.yml/badge.svg)](https://github.com/scrapyard-io/tubes/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/scrapyard-io/tubes.svg)](https://packagist.org/packages/scrapyard-io/tubes)
[![Total Downloads](https://img.shields.io/packagist/dt/scrapyard-io/tubes.svg)](https://packagist.org/packages/scrapyard-io/tubes)
[![License](https://img.shields.io/packagist/l/scrapyard-io/tubes.svg)](LICENSE)
[![Docs](https://img.shields.io/badge/docs-ScrapyardIO-0ea5e9?logo=readthedocs&logoColor=white)](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/overview)

The ScrapyardIO **display companion** for framework **0.7**. Tubes owns the draw → framebuffer → present pipeline: Managed and Deferred framebuffers, OS windows, IC panels, fonts, `Renderer2D`, and Human Input.

Namespace: `ScrapyardIO\Tubes\`.

## Install

```bash
composer require scrapyard-io/tubes:^0.7.0
php workshop vendor:publish --tag=tubes-config
```

Discovery registers `TubesServiceProvider` and MagicAliases `Framebuffer` / `Window` / `Panel` / `Font`.

Install a GFX companion stack:

```bash
php workshop install:gfx
```

Smoke-test the default canvas profile:

```bash
./runner canvas-window-demo
```

## Official documentation

Docs live on the [ScrapyardIO website](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/overview):

- [Overview](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/overview)
- [Installation](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/installation)
- [Basics](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/basics)
- [Configuration](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/configuration)
- [Commands](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/commands)
- [Canvas Window Demo](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/canvas-window-demo)
- [Demo Sketches](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/demo-sketches)
- [Components](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/contracts) (Contracts, Framebuffers, Rendering, Fonts, Canvas, Windows, Panels, Human Input, Inputs)
- [Diving Deeper](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/diving-deeper)
- [Related](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/related) — [gpio-framework](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/gpio-framework/0.7.x/overview) · [UX](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/ux/0.7.x/overview)

## Component splits

This umbrella replaces splittable `tubes/*` packages at `self.version`. Prefer requiring **`scrapyard-io/tubes`**.

| Composer | Folder |
|---|---|
| `tubes/contracts` | `src/Tubes/Contracts` |
| `tubes/framebuffers` | `src/Tubes/Framebuffers` |
| `tubes/rendering` | `src/Tubes/Rendering` |
| `tubes/fonts` | `src/Tubes/Fonts` |
| `tubes/canvas` | `src/Tubes/Canvas` |
| `tubes/windows` | `src/Tubes/Windows` |
| `tubes/panels` | `src/Tubes/Panels` |
| `tubes/human-input` | `src/Tubes/HumanInput` |
| `tubes/inputs` | `src/Tubes/Inputs` |

**Core** stays umbrella-only (providers, MagicAliases, canvas profile support) — no `tubes/core` package.

Legacy replace keys for 0.6 consumers: `tubes/monochrome`, `tubes/color`, `tubes/matrix`, `tubes/epaper`.

## Contributing

Thank you for considering contributing to Tubes! Please open issues and pull requests on [GitHub](https://github.com/scrapyard-io/tubes).

## License

Tubes is open-sourced software licensed under the [MIT license](LICENSE).
