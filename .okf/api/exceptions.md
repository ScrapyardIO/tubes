---
type: CoreType
title: Exceptions
description: OSApplicationException and WindowableException
resource: /src/Tubes/Contracts/Windows/Exceptions/OSApplicationException.php
tags: [tubes, api]
status: stable
generated: { by: cursor-agent/grok-4.6, at: "2026-08-16T13:39:00Z" }
verified:
    - { by: human:angel@projectsaturnstudios.com, at: "2026-08-16T13:46:00Z"}
sources:
  - id: os-ex
    resource: /src/Tubes/Contracts/Windows/Exceptions/OSApplicationException.php
    title: OSApplicationException
  - id: win-ex
    resource: /src/Tubes/Contracts/Windows/Exceptions/WindowableException.php
    title: WindowableException
---

# OSApplicationException

`Tubes\Contracts\Windows\Exceptions\OSApplicationException` extends `Exception`.[^os-ex]

| Factory | Message |
|---------|---------|
| `windowAlreadyCreated(string $name)` | `"Window $name already exists."` |
| `windowNotCreated(string $name)` | `"Window $name does not exist."` |

# WindowableException

`Tubes\Contracts\Windows\Exceptions\WindowableException` extends `Exception` with an empty body.[^win-ex]

[^os-ex]: OSApplicationException
[^win-ex]: WindowableException
