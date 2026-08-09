---
type: Playbook
title: Path-require from tubes-dev
description: Use the tubes-dev skeleton path repository (scrapyard-io/* symlink) to develop this package locally.
tags: [playbook, composer, path-repo, tubes-dev]
generated: { by: okf-documentation-generator/cursor, at: "2026-08-08T21:45:00Z" }
status: draft
sources:
  - id: tubes-dev-composer
    resource: /Users/angelgonzalez/Development/PHP/tubes-dev/composer.json
    title: tubes-dev path repository and scrapyard-io/tubes require
  - id: package-composer
    resource: composer.json
    title: This package composer.json
---

# Context

`tubes-dev` is a ScrapyardIO skeleton app used to develop tubes locally. It requires `scrapyard-io/tubes` and maps `scrapyard-io/*` as a Composer **path** repository with `symlink: true`.[^tubes-dev-composer]

This package checkout lives at:

`tubes-dev/scrapyard-io/tubes`

# Steps

1. Work in the tubes-dev app root (sibling of `scrapyard-io/`).

2. Confirm `composer.json` includes:

```json
"require": {
  "scrapyard-io/tubes": "*"
},
"repositories": [
  {
    "type": "path",
    "url": "scrapyard-io/*",
    "options": {
      "symlink": true
    }
  }
]
```

[^tubes-dev-composer]

3. From tubes-dev:

```bash
composer update scrapyard-io/tubes
```

Composer should symlink `vendor/scrapyard-io/tubes` → `scrapyard-io/tubes`.

4. Confirm discovery lists this package’s provider from `extra.scrapyard-io.providers` → `ScrapyardIO\Tubes\Core\Providers\TubesServiceProvider`.[^package-composer]

5. Run package Pest tests **from tubes-dev** (so path-framework Nab includes `Splices4Bits`):

```bash
./vendor/bin/pest --compact scrapyard-io/tubes/tests/Framebuffers
```

Standalone `composer install` inside `scrapyard-io/tubes` may resolve Packagist Nab without that trait until framework 0.7 Nab ships it.

# Verify

- `composer show scrapyard-io/tubes` resolves from the path repo.
- `vendor/scrapyard-io/tubes` is a symlink into `scrapyard-io/tubes`.
- Class exists: `ScrapyardIO\Tubes\Core\Providers\TubesServiceProvider`.
- MagicAlias class exists: `ScrapyardIO\Tubes\Core\MagicAliases\Framebuffer`.

# Related

- [Package (0.7)](../orientation/package.md)
- [TubesServiceProvider](../core/tubes-service-provider.md)

[^tubes-dev-composer]: tubes-dev path repository and scrapyard-io/tubes require
[^package-composer]: This package composer.json
