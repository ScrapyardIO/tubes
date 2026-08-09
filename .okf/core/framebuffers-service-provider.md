---
type: Module
title: FramebuffersServiceProvider
description: Binds FramebufferManager as deferred singleton framebuffer; companions extendManaged/extendDeferred after resolving. Built-in drivers live on the manager.
resource: src/Tubes/Framebuffers/Providers/FramebuffersServiceProvider.php
tags: [core, framebuffers, provider, magic-alias]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-08T23:00:00Z" }
status: draft
sources:
  - id: fb-sp
    resource: src/Tubes/Framebuffers/Providers/FramebuffersServiceProvider.php
    title: FramebuffersServiceProvider source
  - id: manager
    resource: src/Tubes/Framebuffers/FramebufferManager.php
    title: FramebufferManager
  - id: alias
    resource: src/Tubes/Core/MagicAliases/Framebuffer.php
    title: Framebuffer MagicAlias
  - id: tubes-sp
    resource: src/Tubes/Core/Providers/TubesServiceProvider.php
    title: Aggregate that registers this provider
---

# Role

`ScrapyardIO\Tubes\Framebuffers\Providers\FramebuffersServiceProvider` extends `Fabricate\NutsAndBolts\ServiceProvider` and implements `DeferrableProvider`.[^fb-sp]

Child of [TubesServiceProvider](tubes-service-provider.md).[^tubes-sp]

# Bindings

| Abstract | Concrete |
|----------|----------|
| `'framebuffer'` | singleton `FramebufferManager`[^manager] |
| `FramebufferManager::class` | same instance |
| `BufferFactory::class` | same instance |

# MagicAlias

`ScrapyardIO\Tubes\Core\MagicAliases\Framebuffer` → accessor `framebuffer`.[^alias]

Discovered via Composer `extra.scrapyard-io.aliases` (`"Framebuffer" => …`).

Never singleton a Framebuffer instance — the alias resolves the **manager**.

# Related

- [Framebuffer factory](framebuffer-factory.md)
- [TubesServiceProvider](tubes-service-provider.md)

[^fb-sp]: FramebuffersServiceProvider source
[^manager]: FramebufferManager
[^alias]: Framebuffer MagicAlias
[^tubes-sp]: Aggregate that registers this provider
