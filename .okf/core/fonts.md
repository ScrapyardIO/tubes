---
type: Core
title: "Fonts / FontManager"
description: GFXFont glyph model + FontManager registry; companions Font::extend like Window/Framebuffer.
tags: [core, fonts, gfxfont, registry]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-09T05:35:00Z" }
status: draft
sources:
  - id: gfxfont
    resource: src/Tubes/Contracts/Fonts/GFXFont.php
    title: GFXFont abstract
  - id: classic
    resource: src/Tubes/Fonts/ClassicFont.php
    title: ClassicFont
  - id: manager
    resource: src/Tubes/Fonts/FontManager.php
    title: FontManager
  - id: alias
    resource: src/Tubes/Core/MagicAliases/Font.php
    title: Font MagicAlias
---

# Role

Tubes owns the **glyph data model** and **font registry**. Drawing text is on `Renderer2D` / `DrawingAPI` (see [Rendering](rendering.md)); fonts themselves never draw.

# Registry (companion plug-in)

Same pattern as Window / Framebuffer:

```php
Font::extend('free-sans-9pt', FreeSans9Pt::class);
Font::font('classic'); // built-in ClassicFont
Font::driver();        // tubes.defaults.font (alias of font())
Font::font();          // same default slug
```

Default slug comes from `config('tubes.defaults.font')` (synced to `fonts.default` / `FONT_DRIVER`); see [tubes config](tubes-config.md).

`scrapyard-io/autopen` registers enabled packs via `AutopenServiceProvider` → `Font::extend()`.

# Built-in

| Slug | Class |
|------|-------|
| `classic` | `ClassicFont` (Adafruit 5×7 column-major) |

# Packaging

Subtree `tubes/fonts` (requires `tubes/contracts`, `fabricate/console`, `fabricate/nuts-and-bolts`); umbrella replace maps it.

# Workshop: `make:font`

`ScrapyardIO\Tubes\Fonts\Console\FontMakeCommand` (registered by `TubesServiceProvider`):

```bash
workshop make:font DemoFont
workshop make:font FreeSans18Pt --from=/path/to/FreeSans18pt7b.h
```

Empty scaffolds leave `$bitmaps = []` so `hasBitmapData()` is false. `--from=` imports an Adafruit GFXfont C header via `Fonts\Support\AdafruitGfxHeader`.

# Related

- [Rendering / Renderer2D](rendering.md)
- [Component subtree packaging](../conventions/component-subtree-packaging.md)
