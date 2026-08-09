---
type: Trap
title: "Window ≠ IC Panel"
description: Final output concretes are siblings — never model an IC Panel as a Window backend or subtype.
tags: [trap, window, ic-panel, architecture]
generated: { by: okf-documentation-generator/cursor, at: "2026-08-08T21:45:00Z" }
status: draft
sources:
  - id: angel-arch
    resource: product-owner-architecture
    title: Angel architecture decisions for tubes 0.7 (session brief)
---

# Trap

Treating an **IC Panel** as “just another Window backend” (or a Window that can be an IC).[^angel-arch]

# Why it hurts

- Collapses two presentation paths that share a **framebuffer medium** but differ in host vs circuit concerns.
- Reintroduces 0.6-style naming collisions and bind fast-paths that couple draw logic to a specific sink.[^angel-arch]
- Blocks sequencing where Window/embedded lands before IC/circuits restore.

# Correct shape

Abstract **output** → concrete **OS Window** *or* concrete **IC Panel** (siblings). See [output model](../orientation/output-model.md).

# Related

- [Draw / buffer / output ownership](draw-buffer-output.md)

[^angel-arch]: Angel architecture decisions for tubes 0.7 (session brief)
