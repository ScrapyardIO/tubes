---
type: Core
title: "Framebuffer"
description: Engine-agnostic draw/blit/damage surface — viewport + host FormatSpec; Managed owns PixelStore, Deferred owns host pixels.
tags: [core, framebuffer]
generated: { by: cursor-agent, at: "2026-08-09T03:40:00Z" }
status: draft
sources:
  - id: framebuffer-php
    resource: src/Tubes/Framebuffers/Framebuffer.php
    title: Abstract Framebuffer
  - id: framebuffer-contract
    resource: src/Tubes/Contracts/Framebuffers/Framebuffer.php
    title: Framebuffer contract
  - id: damage
    resource: src/Tubes/Contracts/Framebuffers/DamageGranularity.php
    title: DamageGranularity
---

# Role

Abstract `Framebuffer` is the shared put-pixels surface. It stores **viewport + host `FormatSpec`** only — not a mandatory `PixelStore`. Callers type-hint the contract and do not care Managed vs Deferred.

# Construction

`new Concrete(width, height, hostFormat)` on the base. Managed children pass a `PixelStore` into `ManagedFramebuffer`. Deferred children keep engine-owned pixels.

# API

| Method | Notes |
|--------|--------|
| `viewportWidth/Height` / `hostFormat` | From base |
| `getPixel` / `setPixel` / `setSegment` / `dump` / `flush` | Abstract on base |
| `setPixels` / `setRegion` / `blitFrom` / `blitTo` | Batch helpers over pixel ops |
| `clear` / `fill` | Default via full-viewport `setSegment`; Managed overrides via store |
| `damageGranularity` | Default whole-surface |
| `preservesContentsOnPresent` | Default `false` |

# Related

- [Managed framebuffers](managed-framebuffers.md)
- [Deferred framebuffer](deferred-framebuffer.md)
- [PixelStore](pixel-store.md)
- [Framebuffer factory](framebuffer-factory.md)
