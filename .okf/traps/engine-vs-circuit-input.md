---
type: Trap
title: "Engine ≠ Circuit input"
description: HumanInput concretes are siblings — never model CircuitInput as an EngineInput backend or subtype.
tags: [trap, engine-input, circuit-input, architecture]
generated: { by: okf-documentation-generator/cursor, at: "2026-08-09T06:50:00Z" }
status: draft
sources:
  - id: angel-arch
    resource: product-owner-architecture
    title: Angel architecture decisions for tubes HumanInput Phase A
---

# Trap

Treating **CircuitInput** as “just another EngineInput backend” (or stuffing GPIO behind `InputHandler`).[^angel-arch]

# Why it hurts

- Collapses two host paths that share a **device medium** but differ in engine vs circuit concerns.
- Couples GPIO restore to gfx companion poll APIs and window/event loops.
- Blocks sequencing where Engine input lands before Circuit/GPIO restore.

# Correct shape

Abstract **HumanInput** → concrete **EngineInput** *or* concrete **CircuitInput** (siblings). See [input model](../orientation/input-model.md).

# Related

- [Window ≠ IC Panel](window-vs-ic-panel.md)
- [HumanInput](../core/human-input.md)

[^angel-arch]: Angel architecture decisions for tubes HumanInput Phase A
