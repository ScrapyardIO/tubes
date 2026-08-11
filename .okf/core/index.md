---
okf_version: "0.2"
---

# Core

* [TubesServiceProvider](tubes-service-provider.md) - AggregateServiceProvider entry for package discovery.
* [Tubes config](tubes-config.md) - Defaults + canvas_profiles; `tubes-config` publish. (`draft`)
* [FramebuffersServiceProvider](framebuffers-service-provider.md) - Binds FramebufferManager + MagicAlias wiring.
* [WindowsServiceProvider](windows-service-provider.md) - Binds WindowManager + MagicAlias `Window`. (`draft`)
* [install:gfx Flow](gfx-install-flow.md) - Host gates, disabled labels, ext → wrapper → gfx. (`draft`)
* [Framebuffer factory](framebuffer-factory.md) - Fluent PendingFramebuffer; extendManaged / extendDeferred. (`draft`)
* [Window factory](window-factory.md) - Fluent `Window::driver()` / `profile()` / `make()` → OSWindow. (`draft`)
* [Panel factory](panel-factory.md) - Fluent `Panel::wrap` / `circuit` / `profile` → PanelIC. (`draft`)
* [SupportsPartialRefresh](supports-partial-refresh.md) - Contracts/Core marker; CPU PARTIAL present. (`draft`)
* [Window loop Flow](window-loop-flow.md) - open → paint/present/poll → close. (`draft`)
* [PixelStore](pixel-store.md) - Packed binary host store; init from FormatSpec + optional Z. (`draft`)
* [Framebuffer](framebuffer.md) - Engine-agnostic draw API; viewport + FormatSpec. (`draft`)
* [Managed framebuffers](managed-framebuffers.md) - Full / DirtyRegions / PageSegment. (`draft`)
* [Deferred framebuffer](deferred-framebuffer.md) - Host-backed abstract; present + isHeadless. (`draft`)
* [OpenGL framebuffer contract](opengl-framebuffer.md) - Empty OpenGL marker over Deferred. (`draft`)
* [Canvas](canvas.md) - OSWindow \| PanelIC presentation surfaces. (`draft`)
* [WindowHandler](window-handler.md) - Companion OS window driver API. (`draft`)
* [Rendering / Renderer2D](rendering.md) - DrawingAPI + borrowed framebuffer set/unset (+ text). (`draft`)
* [Fonts / FontManager](fonts.md) - GFXFont registry; Font::extend companions. (`draft`)
* [HumanInput](human-input.md) - EngineInput \| CircuitInput host; devices + controls. (`draft`)
* [InputHandler](input-handler.md) - Companion engine input API + support matrix. (`draft`)
* [CanvasWindowDemo sketch](metal-canvas-sketch.md) - Package `canvas-window-demo` + SoftRenderer2D; SketchRegistry from TubesServiceProvider. (`draft`)
