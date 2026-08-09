---
type: Core
title: "WindowHandler"
description: Engine-owned OS window driver — FormatSpec at construct; binds package DeferredFramebuffer; native present (no PHP flush).
tags: [core, window, canvas, deferred]
generated: { by: cursor-agent, at: "2026-08-09T04:10:00Z" }
status: draft
sources:
  - id: handler
    resource: src/Tubes/Windows/WindowHandler.php
    title: Abstract WindowHandler
  - id: oswindow
    resource: src/Tubes/Canvas/OSWindow.php
    title: OSWindow canvas
---

# Role

`WindowHandler` is what each `*-gfx` package implements to drive a native window. It **defines `FormatSpec` in the constructor** (matching that package’s DeferredFramebuffer) so windowed present never remuxes packings through PHP `flush()`. Companions: sdl3-gfx, ogx, metal-gfx, vulkan-gfx, **cuda-gfx**.

# Lifecycle

```text
new Handler(title, w, h)  →  defineFormatSpec()
open()                    →  bootNative() + bindFramebuffer()
framebuffer()             →  DeferredFramebuffer (engine pixels)
present()                 →  presentNative()   // no PHP pack
pollEvents() / shouldClose()
close()                   →  destroyNative()
```

# Companion hooks (abstract)

| Hook | Purpose |
|------|---------|
| `defineFormatSpec()` | Fixed host packing for this engine |
| `bootNative()` | Create / show native window + context |
| `bindFramebuffer()` | Usually `XxxFramebuffer::attachedTo(...)` |
| `presentNative()` | Swap / renderPresent (bypass PHP flush) |
| `pollNative()` | Event pump (macOS visibility) |
| `shouldClose()` | User close request |
| `destroyNative()` | Tear down (idempotent) |

# Canvas wiring

Preferred (factory / MagicAlias):

```php
$window = Window::driver('sdl3')->title('Demo')->size(800, 600)->open();
$window->framebuffer()->fill(0)->setPixel(10, 10, 0xFFFFFFFF);
$window->present()->pollEvents();
$window->close();
```

Direct handler wrap (tests / low-level):

```php
$window = new OSWindow(new SDL3WindowHandler('Demo', 800, 600));
```

# Related

- [Window factory](window-factory.md)
- [Canvas / OSWindow](canvas.md)
- [Deferred framebuffer](deferred-framebuffer.md)
- [Output model](../orientation/output-model.md)
