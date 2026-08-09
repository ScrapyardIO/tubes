---
type: Orientation
title: "Input model (Engine | Circuit)"
description: Abstract HumanInput resolves to sibling concretes EngineInput or CircuitInput; devices are the shared medium.
tags: [orientation, input, engine, circuit, human-input, architecture]
generated: { by: okf-documentation-generator/cursor, at: "2026-08-09T06:50:00Z" }
status: draft
sources:
  - id: angel-arch
    resource: product-owner-architecture
    title: Angel architecture decisions for tubes HumanInput Phase A
  - id: scaffold
    resource: src/Tubes/HumanInput
    title: HumanInput / EngineInput / CircuitInput + Inputs/InputHandler
---

# Model

The abstract **human-input host** resolves to one of two sibling concretes:[^angel-arch]

| Concrete | Role |
|----------|------|
| **EngineInput** | Desktop / gfx engine path — wraps companion `InputHandler` (like OSWindow wraps WindowHandler) |
| **CircuitInput** | GPIO / circuit path — hollow for later; not an EngineInput backend |

These are **siblings** under abstract `HumanInput` — **not** “Circuit as an EngineInput driver.”[^angel-arch]

On disk: `HumanInput` (`EngineInput` | `CircuitInput`) plus `Inputs/InputHandler` for companion OS/engine drivers.[^scaffold]

# Intended pipeline

```text
companion pollNative / GPIO read  →  devices (Keyboard, Mouse, pads)  →  HumanInput accessors
                                         ↑                                      ↑
                                  shared medium                          EngineInput | CircuitInput
```

- **Devices** (Keyboard, Mouse, GamePad, GameController) and **controls** (DigitalButton, AnalogButton, AnalogStick) are **not** HumanInput subclasses — they are the shared medium.[^angel-arch]
- **EngineInput:** thin wrapper; `poll()` and device getters delegate to `InputHandler`.
- **CircuitInput:** abstract hollow sibling; wire GPIO later without collapsing into EngineInput.
- Callers should not care whether the host is Engine or Circuit once devices are filled.

# Sequencing (0.7 restore)

| Path | Notes |
|------|-------|
| Engine / gfx | Lands first with companion InputHandler subclasses |
| Circuit / GPIO | Depends on circuit restore **later** under CircuitInput[^angel-arch] |

# Related

- [Engine ≠ Circuit input](../traps/engine-vs-circuit-input.md)
- [HumanInput](../core/human-input.md)
- [InputHandler](../core/input-handler.md)
- [Output model (Window \| IC Panel)](output-model.md)

[^angel-arch]: Angel architecture decisions for tubes HumanInput Phase A
[^scaffold]: `HumanInput`/`EngineInput`/`CircuitInput`/`InputHandler` on disk; CircuitInput hollow; gfx InputHandler subclasses live in companion packages
