---
type: Core
title: OpenGL framebuffer contract
description: "OpenGLFramebuffer is an empty marker extending DeferredFramebuffer; present/isHeadless live on Deferred."
tags: [core, framebuffer, deferred, opengl, contract]
generated: { by: "cursor-agent", at: "2026-08-09T03:40:00Z" }
status: draft
sources:
  - id: contract
    resource: src/Tubes/Contracts/Framebuffers/OpenGLFramebuffer.php
    title: OpenGLFramebuffer interface
  - id: deferred
    resource: src/Tubes/Contracts/Framebuffers/DeferredFramebuffer.php
    title: DeferredFramebuffer
---

# Role

`OpenGLFramebuffer` is a **marker** for OpenGL-context deferred buffers (e.g. `microscrap/ogx`). `present()` and `isHeadless()` live on [`DeferredFramebuffer`](deferred-framebuffer.md).

Do not name a concrete class `OpenGlFramebuffer` on Darwin (OpenGL extension classname collision) — use `OgxFramebuffer`.

# Related

- [Deferred framebuffer](deferred-framebuffer.md)
- [Framebuffer factory](framebuffer-factory.md)
