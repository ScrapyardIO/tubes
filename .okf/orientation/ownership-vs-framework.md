---
type: Orientation
title: Ownership vs framework
description: Tubes owns the ScrapyardIO display surface as an opt-in companion; framework 0.7 intentionally removed displays from core.
tags: [orientation, ownership, framework, companion, 0.7]
generated: { by: okf-documentation-generator/cursor, at: "2026-08-08T21:45:00Z" }
status: draft
sources:
  - id: composer
    resource: composer.json
    title: Package description and scrapyard-io provider discovery
  - id: angel-arch
    resource: product-owner-architecture
    title: Angel architecture decisions for tubes 0.7 (session brief)
---

# Decision

`scrapyard-io/tubes` **owns** the ScrapyardIO **display** surface.[^angel-arch]

Framework **0.7** intentionally removed displays, framebuffers, UX, sensors, actuators, and circuits from core so the umbrella stays slim. Companions (including tubes) bring those domains back as **opt-in** packages.[^angel-arch]

# Implications

1. Apps that need panels/windows **require** tubes (or another display companion) — display is not assumed present with framework alone.
2. Do **not** reintroduce display/framebuffer orchestration into `scrapyard-io/framework` “for convenience” (see [dependency direction](../conventions/dependency-direction.md)).
3. Tubes keeps its own providers under `ScrapyardIO\Tubes\…` and discovers via `extra.scrapyard-io.providers`.[^composer]

# Related

- [Package (0.7)](package.md)
- [Companion package](../conventions/companion-package.md)
- [Output model](output-model.md)

[^composer]: Package description and scrapyard-io provider discovery
[^angel-arch]: Angel architecture decisions for tubes 0.7 (session brief)
