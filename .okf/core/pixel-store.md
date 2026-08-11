---
type: Core
title: "PixelStore"
description: Packed binary host store sized from FormatSpec and optional Z layers; mutations write host packing.
tags: [core, framebuffer, pixel-store, format-spec]
generated: { by: cursor-agent, at: "2026-08-08T22:30:00Z" }
status: draft
sources:
  - id: pixel-store-php
    resource: src/Tubes/Framebuffers/PixelStore.php
    title: PixelStore implementation
  - id: pixel-store-contract
    resource: src/Tubes/Contracts/Framebuffers/PixelStore.php
    title: PixelStore contract
  - id: format-spec
    resource: src/Tubes/Contracts/Framebuffers/FormatSpec.php
    title: FormatSpec bytesForSurface
---

# Role

`PixelStore` is the **packed binary** host blob for Managed framebuffers. It is not a PHP int grid.

# Init

Construct with:

* `width`, `height` (positive)
* host `FormatSpec` (working packing)
* `z` layer count (default **1**; must be ≥ 1)

Allocates `str_repeat("\0", bytesForSurface(w,h) × z)`.

`FormatSpec::bytesForSurface()` sizes one layer:

| PixelFormat | Size rule |
|-------------|-----------|
| `MONO_VERTICAL_PAGE` | `width × ceil(height/8)` |
| `MONO_HORIZONTAL` | `height × ceil(width/8)` |
| `PLANAR` | mono-horizontal plane × `ChannelPalette::count()` (palette required) |
| `ROW_MAJOR` | B12: `ceil(pixels/2)×3`; else `pixels × ceil(bitDepth/8)` |

# Mutations

Host-packing writers (clip off-surface; bad layer throws):

* `clear(?layer)` / `fill($color, ?layer)`
* `getPixel` / `setPixel` / `setPixels` / `setSegment`
* Draw colours may be `0xRRGGBBAA`; `PixelColorPack::packDrawColor` packs into host depth on write (no FormatSpec alloc per pixel).
* ROW_MAJOR solid `fill` / `setSegment` (TOP_TO_BOTTOM, non-B12): pack once; whole layer via one `str_repeat`, partial via **in-place** byte writes (never per-row `substr_replace` — copies the whole store and destroys FPS).
* `BOTTOM_TO_TOP` flips Y for logical coordinates
* Mono uses `BitOrder`; row-major uses `Endianness`; planar maps colour → channel planes

# Contract surface

Accessors: `width()`, `height()`, `z()`, `hostFormat()`, `layerByteLength()`, `byteLength()`, `dump(?layer)`.

# Non-goals (on PixelStore)

Dirty tracking and flush-to-foreign-spec conversion stay on Framebuffer / Managed policy. Drawing API for callers is on {@see Framebuffer}.
