---
type: Orientation
title: Ecosystem docs (website)
description: Website 0.7.x page inventory for scrapyard-io/tubes — gpio-style Basics + Components + Diving Deeper; sync via ecosystem:sync.
tags: [orientation, docs, ecosystem, website]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-11T19:30:00Z" }
status: draft
sources:
  - id: seeders
    resource: scrapyard-io.projectsaturnstudios database/seeders/content/ecosystem/scrapyard-io/tubes/0.7.x/
    title: Markdown page bodies
  - id: manifest
    resource: scrapyard-io.projectsaturnstudios app/Support/EcosystemContentManifest.php
    title: tubes07Pages()
---

# Role

Official human docs for this package live on the ScrapyardIO website under `/ecosystem/scrapyard-io/tubes/0.7.x/…` — not in this package tree (except the Packagist README, which links out).

Version line: **`0.7.x` only** (no `0.6.x` / `0.1.x` maintenance series).

# Page inventory (`tubes07Pages()`)

| Nav | Slugs |
|-----|-------|
| Prologue | `overview`, `related` |
| Getting Started | `installation`, `requirements`, `basics`, `configuration`, `commands`, `canvas-window-demo`, `demo-sketches` |
| Components (no Core) | `contracts`, `framebuffers`, `rendering`, `fonts`, `canvas`, `windows`, `panels`, `human-input`, `inputs` |
| Diving Deeper | `diving-deeper` |
| Reference | `reference`, `troubleshooting` |

Must cover: demo sketches, `tubes-config`, Workshop commands (`install:gfx` / `uninstall:gfx` / `make:font` / `about` Drivers), links to **gpio-framework** and **UX**.

# Sync (website repo)

```bash
php artisan ecosystem:sync scrapyard-io/tubes
```

Sources: `database/seeders/content/ecosystem/scrapyard-io/tubes/0.7.x/*.md`.

# Related

- [Package (0.7)](package.md)
- [Component subtree packaging](../conventions/component-subtree-packaging.md)
- [CanvasWindowDemo sketch](../core/metal-canvas-sketch.md)
- [Tubes config](../core/tubes-config.md)
