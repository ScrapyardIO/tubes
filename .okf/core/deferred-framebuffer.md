---
type: Core
title: "Deferred framebuffer"
description: Host-backed abstract — present() + isHeadless(); engine owns pixels (SDL/GL/Metal/Vulkan).
tags: [core, framebuffer, deferred]
generated: { by: cursor-agent, at: "2026-08-09T03:40:00Z" }
status: draft
sources:
  - id: deferred-abstract
    resource: src/Tubes/Framebuffers/DeferredFramebuffer.php
    title: Abstract DeferredFramebuffer
  - id: deferred-contract
    resource: src/Tubes/Contracts/Framebuffers/DeferredFramebuffer.php
    title: DeferredFramebuffer contract
---

# Role

`DeferredFramebuffer` is the host-backed lane. Pixels live in SDL / OpenGL / Metal / Vulkan / … — not a tubes `PixelStore`. Contract adds `present()` and `isHeadless()` so every deferred engine shares the same finish API.

Companions extend `ScrapyardIO\Tubes\Framebuffers\DeferredFramebuffer` and register via `extendDeferred` and/or published `config/framebuffers/<slug>.php`.

# Related

- [Framebuffer](framebuffer.md)
- [OpenGL framebuffer contract](opengl-framebuffer.md) — empty marker over Deferred
- [Framebuffer factory](framebuffer-factory.md)
