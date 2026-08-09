---
type: Module
title: WindowsServiceProvider
description: Binds WindowManager as window / WindowFactory; MagicAlias Window; publishes windows config.
tags: [core, provider, window, magic-alias, config]
generated: { by: cursor-agent, at: "2026-08-09T04:20:00Z" }
status: draft
sources:
  - id: provider
    resource: src/Tubes/Windows/Providers/WindowsServiceProvider.php
    title: WindowsServiceProvider
  - id: alias
    resource: src/Tubes/Core/MagicAliases/Window.php
    title: Window MagicAlias
---

# Role

`ScrapyardIO\Tubes\Windows\Providers\WindowsServiceProvider` binds the deferred singleton `'window'` → `WindowManager`, aliases `WindowManager` / `WindowFactory`, merges `config/windows.php`, and on boot publishes `tubes-windows-config`.

When the app has `configPath()`, it also merges slug files from `config/windows/*.php`.

# MagicAlias

`ScrapyardIO\Tubes\Core\MagicAliases\Window` → accessor `window`. Composer `extra.scrapyard-io.aliases` maps `"Window"`.

# Related

- [Window factory](window-factory.md)
- [TubesServiceProvider](tubes-service-provider.md)
