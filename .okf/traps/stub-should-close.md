---
type: Trap
title: Stub shouldClose returns true
description: "Companion WindowHandler stubs return shouldClose() === true until pollNative is real."
tags: [trap, window, stub]
generated: { by: "cursor-agent/grok-4.5", at: "2026-08-09T04:15:00Z" }
status: draft
---

# Trap

Until a companion implements native boot, `shouldClose()` often returns `true` so tests can assert the stub.

Do **not** write production loops like `while (! $window->shouldClose())` against those stubs — the loop never runs. After `bootNative` / `pollNative` land, `shouldClose` must track the real close flag.
