---
type: CoreType
title: OSWindow
description: Magic alias accessor for window
resource: /src/Tubes/Windows/OSWindow.php
tags: [tubes, api]
status: stable
generated: { by: cursor-agent/grok-4.6, at: "2026-08-16T13:39:00Z" }
verified:
    - { by: human:angel@projectsaturnstudios.com, at: "2026-08-16T13:46:00Z"}
sources:
  - id: alias
    resource: /src/Tubes/Windows/OSWindow.php
    title: OSWindow.php
---

# Schema

`Tubes\Windows\OSWindow` extends `Fabricate\MagicAliases\MagicAlias`. `getMagicAliasAccessor()` returns `'window'`.[^alias]

`OSWindowsServiceProvider` binds `'window'` to `WindowManager`.

[^alias]: OSWindow.php
