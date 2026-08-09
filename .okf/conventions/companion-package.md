---
type: Convention
title: Companion package
description: tubes is an opt-in ScrapyardIO companion with ScrapyardIO\Tubes providers — not a Fabricate domain owned by Core.
tags: [convention, companion, provider, discovery]
generated: { by: okf-documentation-generator/cursor, at: "2026-08-08T21:45:00Z" }
status: draft
sources:
  - id: composer
    resource: composer.json
    title: extra.scrapyard-io.providers and package identity
  - id: tubes-sp
    resource: src/Tubes/Core/Providers/TubesServiceProvider.php
    title: Package-owned aggregate provider
  - id: angel-arch
    resource: product-owner-architecture
    title: Angel architecture decisions for tubes 0.7 (session brief)
---

# Rule

`scrapyard-io/tubes` is a **companion** to `scrapyard-io/framework` 0.7, not a Fabricate domain under `Fabricate\*`.[^composer][^angel-arch]

Therefore:

1. It **owns** providers under `ScrapyardIO\Tubes\…` (starting with [TubesServiceProvider](../core/tubes-service-provider.md)).[^tubes-sp]
2. Discovery uses Composer `extra.scrapyard-io.providers`.[^composer]
3. Knowledge for this package lives at **package-root** `.okf/` only — never nest `.okf` under `src/Tubes/*`.[^angel-arch]
4. Display remains **opt-in**; apps without tubes do not get panel/window surfaces from framework alone.[^angel-arch]

# Related

- [Ownership vs framework](../orientation/ownership-vs-framework.md)
- [Dependency direction](dependency-direction.md)

[^composer]: extra.scrapyard-io.providers and package identity
[^tubes-sp]: Package-owned aggregate provider
[^angel-arch]: Angel architecture decisions for tubes 0.7 (session brief)
