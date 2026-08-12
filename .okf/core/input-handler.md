---
type: Core
title: "InputHandler"
description: Companion engine input driver API wrapped by EngineInput; engine support matrix.
tags: [core, input-handler, companions, human-input]
generated: { by: okf-documentation-generator/cursor, at: "2026-08-09T06:50:00Z" }
status: draft
sources:
  - id: scaffold
    resource: src/Tubes/Inputs/InputHandler.php
    title: InputHandler abstract companion
  - id: angel-arch
    resource: product-owner-architecture
    title: Engine support matrix for HumanInput
---

# Role

`ScrapyardIO\Tubes\Inputs\InputHandler` is the engine-owned companion API (mirrors `Windows\WindowHandler`).[^scaffold]

Protected state: `?Keyboard $keyboard`, `?Mouse $mouse`, `array $game_pads`, `array $game_controllers`.

- Abstract `poll(): static` — companions fill device state here.
- Concrete getters: `keyboard()`, `mouse()`, `gamePads()`, `gameControllers()`.

`EngineInput` constructs with an `InputHandler` and delegates.

# Engine support matrix

| Target | Package | Notes |
|--------|---------|-------|
| SDL3 | microscrap/sdl3-gfx | Fan out in `SDL3WindowHandler::pollNative` |
| OpenGL | microscrap/ogx | After `Window::pollEvents`, glfw Input |
| Vulkan | microscrap/vulkan-gfx | Same GLFW as ogx |
| CUDA | microscrap/cuda-gfx | Same GLFW as ogx |
| Metal binding | microscrap/metal / ext-metal | `mtl_input_*` (0.7.3+) |
| Metal gfx | microscrap/metal-gfx | `MetalInputHandler`; fan-out in `MetalWindowHandler::pollNative` |
| open-gl/vulkan/cuda bindings | — | Draw/compute only, not input hosts |
| Circuit/GPIO | `CircuitInput` (Waveforms ButtonPad/GameController) | Not an InputHandler backend |

# Related

- [HumanInput](human-input.md)
- [Input model](../orientation/input-model.md)
- [WindowHandler](window-handler.md)

[^scaffold]: `src/Tubes/Inputs/InputHandler.php`
[^angel-arch]: Angel architecture decisions for tubes HumanInput Phase A
