---
type: Convention
title: Dependency direction
description: Do not move display, framebuffers, or tubes orchestration back into scrapyard-io/framework core.
tags: [convention, dependency, framework, display]
generated: { by: okf-documentation-generator/cursor, at: "2026-08-08T21:45:00Z" }
status: draft
sources:
  - id: angel-arch
    resource: product-owner-architecture
    title: Angel architecture decisions for tubes 0.7 (session brief)
  - id: composer
    resource: composer.json
    title: Tubes package identity as display companion
---

# Rule

Framework **0.7** removed displays, framebuffers, UX, sensors, actuators, and circuits from core on purpose.[^angel-arch]

**Do not:**

- Add display/framebuffer/panel/window types back into `scrapyard-io/framework` to “make tubes thinner.”
- Teach framework Core to own VisualPresentation / display registries for tubes surfaces.
- Treat tubes as a temporary holding pen before merging display into the umbrella again.

**Do:**

- Keep orchestration and public display APIs in `scrapyard-io/tubes` (and related companions as they exist).[^composer]
- Depend on framework NutsAndBolts / Chassis / Machine APIs as peers need — without reversing ownership of the display domain.[^angel-arch]

# Related

- [Companion package](companion-package.md)
- [Ownership vs framework](../orientation/ownership-vs-framework.md)

[^angel-arch]: Angel architecture decisions for tubes 0.7 (session brief)
[^composer]: Tubes package identity as display companion
