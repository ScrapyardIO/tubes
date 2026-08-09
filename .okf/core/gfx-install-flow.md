---
type: Core
title: install:gfx Flow
description: DetectHost → prompt (disabled labels) → EnsurePhpExtension → EnsureExtensionWrapper → ComposerRequireGfx → publish.
tags: [core, workshop, flow, gfx, install]
generated: { by: cursor-agent, at: "2026-08-09T04:30:00Z" }
status: draft
sources:
  - id: flow
    resource: src/Tubes/Core/Workflows/GfxInstall/GfxInstallFlow.php
    title: GfxInstallFlow
  - id: enum
    resource: src/Tubes/Core/Enums/GfxCompanionTarget.php
    title: GfxCompanionTarget
---

# Role

Workshop `install:gfx` orchestration for microscrap GFX companions.

# Graph

```text
DetectHostNode
  → PromptGfxTargetsNode
  → EnsurePhpExtensionNode      # PIE / ext-*
  → EnsureExtensionWrapperNode  # microscrap bindings (sdl3, metal, glfw+open-gl, …)
  → ComposerRequireGfxNode      # *-gfx packages only
  → PublishFramebufferConfigNode
```

# Prompt gating

- **Allowed** only when OS/arch/native libs pass and the primary gfx package is not already installed.
- **Disabled + label** for incompatible (`— unavailable on Darwin`, `— SDL3 lib not installed`, …) and already-installed (`— already installed`).
- Darwin: CUDA always disabled (`unavailable on Darwin`). CUDA OS family is Linux-only; still hollow until `cuda-gfx` is ready.
- SDL3 / OpenGL / Vulkan require their native libs (pkg-config / dylib / framework probes).

# Related

- [TubesServiceProvider](tubes-service-provider.md) — registers the Workshop command
- [Framebuffer factory](framebuffer-factory.md)
