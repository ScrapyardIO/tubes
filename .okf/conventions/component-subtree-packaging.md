---
type: Convention
title: Component subtree packaging
description: Each tubes component folder ships composer.json + LICENSE.md + .gitattributes for git subtree split; umbrella replace maps tubes/* at self.version.
tags: [convention, composer, replace, subtree]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-08T23:15:00Z" }
status: draft
sources:
  - id: umbrella
    resource: composer.json
    title: scrapyard-io/tubes replace map
  - id: fb-composer
    resource: src/Tubes/Framebuffers/composer.json
    title: tubes/framebuffers component package
  - id: contracts-composer
    resource: src/Tubes/Contracts/composer.json
    title: tubes/contracts component package
  - id: donor
    resource: OfficialScrapyardIO tubes Color/Monochrome component packaging
    title: 0.6 tubes component subtree pattern
---

# Rule

Splittable components under `src/Tubes/{Component}/` carry their own packaging (Illuminate / Fabricate style):[^donor]

| File | Purpose |
|------|---------|
| `composer.json` | Split package name (`tubes/framebuffers`, …), PSR-4 root `""` for that folder |
| `LICENSE.md` | MIT |
| `.gitattributes` | `/.github` + `.gitattributes` `export-ignore` |

Umbrella `scrapyard-io/tubes` **replaces** those split names at `self.version` so apps require the kitchen-sink package only.[^umbrella]

# Current split packages

| Folder | Package |
|--------|---------|
| `Contracts/` | `tubes/contracts`[^contracts-composer] |
| `Framebuffers/` | `tubes/framebuffers` (requires `tubes/contracts`)[^fb-composer] |
| `Rendering/` | `tubes/rendering` (requires `tubes/contracts`) |
| `Fonts/` | `tubes/fonts` (requires `tubes/contracts`, `fabricate/console`, `fabricate/nuts-and-bolts`) |
| `Canvas/` | `tubes/canvas` (requires `tubes/contracts`) |
| `Windows/` | `tubes/windows` (requires `tubes/contracts`, `fabricate/nuts-and-bolts`) |
| `Panels/` | `tubes/panels` (requires `tubes/contracts`, `fabricate/nuts-and-bolts`, `scrapyard-io/gpio-framework`) |
| `HumanInput/` | `tubes/human-input` (requires `tubes/contracts`, `waveforms/contracts`) |
| `Inputs/` | `tubes/inputs` (requires `tubes/contracts`, `tubes/human-input`) |

**Not split:** `Core/` stays umbrella-only (providers, MagicAliases, canvas profile support) — no `tubes/core` package or replace entry.

Legacy replace keys kept for 0.6 consumers: `tubes/monochrome`, `tubes/color`, `tubes/matrix`, `tubes/epaper`.

# Subtree split (operator)

From a monorepo that tracks this tree, split a component path to its remote, e.g.:

```bash
git subtree split --prefix=src/Tubes/Framebuffers -b tubes-framebuffers
git push tubes-framebuffers-remote tubes-framebuffers:main
```

(Exact remote / prefix depends on how the umbrella is nested in the parent repo.)

# Related

- [Companion package](companion-package.md)
- [Framebuffer factory](../core/framebuffer-factory.md)

[^umbrella]: scrapyard-io/tubes replace map
[^fb-composer]: tubes/framebuffers component package
[^contracts-composer]: tubes/contracts component package
[^donor]: 0.6 tubes component subtree pattern
