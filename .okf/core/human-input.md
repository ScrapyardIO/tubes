---
type: Core
title: "HumanInput"
description: Abstract host (EngineInput | CircuitInput); devices and controls are mediums, not subclasses.
tags: [core, human-input, engine-input, circuit-input, devices]
generated: { by: okf-documentation-generator/cursor, at: "2026-08-09T06:50:00Z" }
status: draft
sources:
  - id: scaffold
    resource: src/Tubes/HumanInput
    title: HumanInput tree Phase A
---

# Role

`ScrapyardIO\Tubes\HumanInput\HumanInput` is the abstract **host** API:[^scaffold]

- `poll(): static`
- `keyboard(): ?Keyboard`
- `mouse(): ?Mouse`
- `gamePads(): array` (list of GamePad)
- `gameControllers(): array` (list of GameController)

# Concretes

| Class | Notes |
|-------|-------|
| `EngineInput` | Wraps `Inputs\InputHandler`; delegates poll + device accessors |
| `CircuitInput` | Concrete GPIO host — `profile(...$names)` / `fromCircuits(...)` maps Waveforms `GameController` → Tubes `GameController` (LEFT/RIGHT sticks + triggers) and Waveforms `ButtonPad`-only → Tubes `GamePad`; `poll()` re-polls circuits and updates controls in place |

# Devices (not HumanInput subclasses)

| Device | Shape |
|--------|-------|
| `Keyboard` | `keys` name→bool; `keys()` / `isDown()` / `setKey()` |
| `Mouse` | x/y, list of `DigitalButton`, wheel_delta; `MouseButton` enum for named buttons |
| `GamePad` | name + DigitalButton\|AnalogButton only — **rejects** AnalogStick |
| `GameController` | name + controls — **requires** ≥1 AnalogStick and ≥1 button |

# Controls (not HumanInput subclasses)

| Control | Range |
|---------|-------|
| `DigitalButton` | pressed bool |
| `AnalogButton` | value 0..1 |
| `AnalogStick` | x/y −1..1 |

Validation throws `HumanInputException` static factories.

# Related

- [Input model](../orientation/input-model.md)
- [InputHandler](input-handler.md)
- [Engine ≠ Circuit input](../traps/engine-vs-circuit-input.md)

[^scaffold]: Phase A skeleton on disk under `src/Tubes/HumanInput` and `src/Tubes/Inputs`
