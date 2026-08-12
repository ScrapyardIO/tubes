---
type: Convention
title: Dependency direction
description: Core may use Core+components; components never use Core. Granular composer requires; never kitchen-sink umbrellas.
tags: [convention, dependency, framework, display, core]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-12T03:20:00Z" }
status: draft
sources:
  - id: angel-arch
    resource: product-owner-architecture
    title: Angel architecture decisions for tubes 0.7 (session brief)
  - id: angel-core
    resource: product-owner-architecture
    title: Angel Core vs component dependency rule 2026-08-11
  - id: composer
    resource: composer.json
    title: Tubes package identity as display companion
---

# Rule

Framework **0.7** removed displays, framebuffers, UX, sensors, actuators, and circuits from Fabricate core on purpose.[^angel-arch]

## Core vs component (all three frameworks)

Applies inside **Fabricate**, **GeneralPurposeIO**, and **ScrapyardIO\Tubes**:

```text
Core        → own Core + own non-core components   ✓
non-core    → other non-core (contracts, peers)    ✓
non-core    → Core (own or foreign)                ✗
```

- Core is the composition root / discovery layer — not a library for components to import.
- If a component needs a capability that today only exists under Core, **move or expose that capability on a non-core seam** (contracts, manager API, inject built objects). Do **not** `class_exists` + “require the kitchen-sink umbrella”.

## Display ownership

**Do not:**

- Add display/framebuffer/panel/window types back into `scrapyard-io/framework` to “make tubes thinner.”
- Teach Fabricate Core to own VisualPresentation / display registries for tubes surfaces.

**Do:**

- Keep orchestration and public display APIs in `scrapyard-io/tubes`.[^composer]
- Depend on **split** `fabricate/*` / `gpio/*` / `waveforms/*` packages only — never kitchen-sink umbrellas in `require` or as a workaround in `suggest`.[^angel-core]

# Related

- [Companion package](companion-package.md)
- [Ownership vs framework](../orientation/ownership-vs-framework.md)
- [Component subtree packaging](component-subtree-packaging.md)

[^angel-arch]: Angel architecture decisions for tubes 0.7 (session brief)
[^angel-core]: Angel Core vs component dependency rule 2026-08-11
[^composer]: Tubes package identity as display companion
