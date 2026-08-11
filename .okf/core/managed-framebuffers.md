---
type: Core
title: "Managed framebuffers"
description: FullFramebuffer, DirtyRegionsBuffer, and PageSegmentBuffer over PixelStore — 0.6 strategies without FormatSpecFramebuffer.
tags: [core, framebuffer, managed, dirty]
generated: { by: cursor-agent, at: "2026-08-08T22:40:00Z" }
status: draft
sources:
  - id: managed-base
    resource: src/Tubes/Framebuffers/ManagedFramebuffer.php
    title: ManagedFramebuffer
  - id: full
    resource: src/Tubes/Framebuffers/FullFramebuffer.php
    title: FullFramebuffer
  - id: dirty
    resource: src/Tubes/Framebuffers/DirtyRegionsBuffer.php
    title: DirtyRegionsBuffer
  - id: pages
    resource: src/Tubes/Framebuffers/PageSegmentBuffer.php
    title: PageSegmentBuffer
  - id: dumped
    resource: src/Tubes/Contracts/Framebuffers/DumpedBuffer.php
    title: DumpedBuffer
---

# Role

Managed = software-owned `PixelStore` + flush policy. There is **no** `FormatSpecFramebuffer` class — host FormatSpec lives on the store; `ManagedFramebuffer` holds emit helpers (`bytesForSpec`, region dump, `formatSpec()` alias).

**Flush passthrough:** when the flush target FormatSpec matches the host, emit dump / dirty slices as-is (no `PixelColorPack`). Convert only on mismatch.

**DirtyRegionsBuffer:** `markDirty` only appends; coalesce runs once in `flush` (avoids O(n²) merges while filling circles with VLines).

# Concretes

| Class | Host constraint | Flush |
|-------|-----------------|--------|
| `FullFramebuffer` | any | whole surface (`string` or `[DumpedBuffer]`) |
| `DirtyRegionsBuffer` | `ROW_MAJOR` | coalesced dirty rects → `DumpedBuffer[]` |
| `PageSegmentBuffer` | `MONO_VERTICAL_PAGE` | coalesced dirty page runs → `DumpedBuffer[]` |

Factory: `FullFramebuffer::sized($w, $h, $hostFormat, $z = 1)` (same on all Managed). Prefer `Framebuffer::driver('full')->size(…)->format(…)->create()` via [Framebuffer factory](framebuffer-factory.md).

# Related

- [Framebuffer factory](framebuffer-factory.md)
- [Framebuffer](framebuffer.md)
- [PixelStore](pixel-store.md)
