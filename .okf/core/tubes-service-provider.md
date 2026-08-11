---
type: Module
title: TubesServiceProvider
description: AggregateServiceProvider discovered via extra.scrapyard-io.providers; merges tubes config, registers Framebuffers + Windows + Fonts, gfx commands, and package sketches.
resource: src/Tubes/Core/Providers/TubesServiceProvider.php
tags: [core, provider, aggregate, discovery]
generated: { by: okf-documentation-generator/cursor, at: "2026-08-08T21:45:00Z" }
status: draft
sources:
  - id: tubes-sp
    resource: src/Tubes/Core/Providers/TubesServiceProvider.php
    title: TubesServiceProvider source
  - id: composer
    resource: composer.json
    title: extra.scrapyard-io.providers
  - id: fb-sp
    resource: src/Tubes/Framebuffers/Providers/FramebuffersServiceProvider.php
    title: Framebuffers child provider
  - id: win-sp
    resource: src/Tubes/Windows/Providers/WindowsServiceProvider.php
    title: Windows child provider
---

# Role

`ScrapyardIO\Tubes\Core\Providers\TubesServiceProvider` is the package’s discovery entry.[^composer] It extends `Fabricate\NutsAndBolts\AggregateServiceProvider` and lists child providers in `$providers`.[^tubes-sp]

# On disk (0.7 reconstituting)

```php
protected array $providers = [
    FramebuffersServiceProvider::class,
    WindowsServiceProvider::class,
    FontsServiceProvider::class,
];
```

`register()` merges package [tubes config](tubes-config.md) (`config/tubes.php` → `tubes`).

`boot()`:

- Syncs `tubes.defaults.framebuffer` / `font` into `framebuffers` / `fonts` `.default`
- Registers Workshop `about` **Drivers** rows via `AboutCommand::add()` — `Canvas` / `Framebuffers` / `Fonts` from `tubes.defaults.*` (see [tubes config](tubes-config.md))
- Publishes `config/tubes.php` under Workshop tag **`tubes-config`**
- Registers package sketches on Fabricate `SketchRegistry` (e.g. [CanvasWindowDemo](metal-canvas-sketch.md) as `canvas-window-demo`) — do **not** rely on `config('sketches.load')` merge for package auto-load

Also registers Workshop commands `install:gfx` / `uninstall:gfx` / `make:font` when console-capable.[^tubes-sp] Those classes must be `singleton`-bound on the container before `commands([...])` — Symfony `ContainerCommandLoader::has()` ignores map entries whose class is not bound (commands vanish from `workshop list`).

# Child providers

- [FramebuffersServiceProvider](framebuffers-service-provider.md)
- [WindowsServiceProvider](windows-service-provider.md)
- FontsServiceProvider

# Discovery

Composer declares:

```json
"extra": {
  "scrapyard-io": {
    "providers": [
      "ScrapyardIO\\Tubes\\Core\\Providers\\TubesServiceProvider"
    ]
  }
}
```

[^composer]

# Related

- [FramebuffersServiceProvider](framebuffers-service-provider.md)
- [WindowsServiceProvider](windows-service-provider.md)
- [Companion package](../conventions/companion-package.md)
- [Package (0.7)](../orientation/package.md)

[^tubes-sp]: TubesServiceProvider source
[^composer]: extra.scrapyard-io.providers
