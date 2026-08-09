---
type: Core
title: Framebuffer factory
description: FramebufferManager + config registry + make(); extendManaged / extendDeferred; MagicAlias Framebuffer.
tags: [core, framebuffer, factory, magic-alias, deferred, managed, config]
generated: { by: cursor-agent, at: "2026-08-09T04:00:00Z" }
status: draft
sources:
  - id: manager
    resource: src/Tubes/Framebuffers/FramebufferManager.php
    title: FramebufferManager
  - id: config
    resource: src/Tubes/Framebuffers/config/framebuffers.php
    title: framebuffers config stub
  - id: alias
    resource: src/Tubes/Core/MagicAliases/Framebuffer.php
    title: Framebuffer MagicAlias
---

# Role

Factory for **registered** framebuffer strategies. Built-ins are managed (`full` / `dirty` / `page`). Companions register deferred creators and/or publish `config/framebuffers/<slug>.php`.

# Fluent build

```php
Framebuffer::make()->size(128, 64)->format($hostSpec)->create();
Framebuffer::driver()->size(128, 64)->format($hostSpec)->create(); // tubes.defaults.framebuffer
Framebuffer::driver('sdl3')->size(320, 240)->format($hostSpec)->create();
Framebuffer::full()->size(128, 64)->format($hostSpec)->get();
```

`driver(...|null)` / `make(?string $driver = null)` use `config('tubes.defaults.framebuffer')` (synced to `framebuffers.default`) when omitted.

# Config

- Root [tubes config](tubes-config.md) (`tubes-config` publish tag) owns MagicAlias defaults.
- Package stub merged as `framebuffers` (`tubes-framebuffers-config` publish tag).
- App `config/framebuffers/*.php` slug files merge into the driver registry (`kind`, `class`, `extension`).
- `create()` fails fast if `extension` is not loaded or `class` is missing.

# Registration

| Method | Lane |
|--------|------|
| `extendManaged($name, callable\|class-string)` | managed |
| `extendDeferred($name, callable\|class-string)` | deferred |

# Related

- [Deferred framebuffer](deferred-framebuffer.md)
- [install:gfx](../orientation/package.md) — TubesServiceProvider registers Workshop install/uninstall Flow commands
