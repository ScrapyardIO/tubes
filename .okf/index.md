---
okf_version: "0.2"
---

# scrapyard-io/tubes Knowledge Bundle

Package knowledge for `scrapyard-io/tubes` (ScrapyardIO display companion, v0.7.0).
Read this index first; open only the concepts needed for the task.

**Trust rule:** Prefer `status: stable`. Treat `deprecated` as historical only. New agent-written concepts stay `status: draft` until a human verifies them.
**Placement:** This bundle lives at the **tubes package root** only — never under `src/Tubes/*`.
**Links:** Concept cross-links use paths relative to each file.
**Scope:** Document what exists on disk in this 0.7 reconstituting tree, plus durable architecture decisions marked `draft`. Do **not** invent 0.6 panel/window APIs unless the PHP files are present.
**Dist note:** `.okf/` and root `AGENTS.md` are `export-ignore` in `.gitattributes` so Composer dist packages do not ship this bundle.

# Orientation

Section index: [orientation/](orientation/index.md)

* [Package (0.7)](orientation/package.md) - Composer identity, namespace, reconstituting surface.
* [Ownership vs framework](orientation/ownership-vs-framework.md) - Tubes owns display; framework 0.7 stays slim.
* [Output model (Window | IC Panel)](orientation/output-model.md) - Sibling concretes under an abstract output; shared framebuffer medium.
* [Input model (Engine | Circuit)](orientation/input-model.md) - Sibling concretes under abstract HumanInput; devices as shared medium. (`draft`)

# Core

Section index: [core/](core/index.md)

* [TubesServiceProvider](core/tubes-service-provider.md) - Aggregate provider registered via `extra.scrapyard-io.providers`.
* [Tubes config](core/tubes-config.md) - Defaults + canvas_profiles; publish tag `tubes-config`. (`draft`)
* [FramebuffersServiceProvider](core/framebuffers-service-provider.md) - Binds FramebufferManager; MagicAlias `Framebuffer`.
* [WindowsServiceProvider](core/windows-service-provider.md) - Binds WindowManager; MagicAlias `Window`. (`draft`)
* [install:gfx Flow](core/gfx-install-flow.md) - Host gates, disabled prompt labels, ext → wrapper → gfx nodes. (`draft`)
* [Framebuffer factory](core/framebuffer-factory.md) - Fluent build + extendManaged / extendDeferred. (`draft`)
* [Window factory](core/window-factory.md) - Fluent `Window::driver()` / `profile()` / `make()` → OSWindow. (`draft`)
* [Window loop Flow](core/window-loop-flow.md) - open → paint/present/poll → close. (`draft`)
* [PixelStore](core/pixel-store.md) - Packed binary host store sized from host FormatSpec + Z. (`draft`)
* [Framebuffer](core/framebuffer.md) - Engine-agnostic draw API; viewport + FormatSpec. (`draft`)
* [Managed framebuffers](core/managed-framebuffers.md) - Full / DirtyRegions / PageSegment. (`draft`)
* [Deferred framebuffer](core/deferred-framebuffer.md) - Host-backed abstract; present + isHeadless. (`draft`)
* [OpenGL framebuffer contract](core/opengl-framebuffer.md) - Empty OpenGL marker over Deferred. (`draft`)
* [Canvas](core/canvas.md) - OSWindow \| PanelIC presentation surfaces. (`draft`)
* [WindowHandler](core/window-handler.md) - Companion OS window driver API. (`draft`)
* [Rendering / Renderer2D](core/rendering.md) - DrawingAPI + borrowed framebuffer set/unset (+ text). (`draft`)
* [Fonts / FontManager](core/fonts.md) - GFXFont registry; Font::extend companions. (`draft`)
* [HumanInput](core/human-input.md) - EngineInput \| CircuitInput host; devices + controls. (`draft`)
* [InputHandler](core/input-handler.md) - Companion engine input API + support matrix. (`draft`)
* [CanvasWindowDemo sketch](core/metal-canvas-sketch.md) - Package-owned `canvas-window-demo` + SoftRenderer2D; registered via TubesServiceProvider. (`draft`)

# Conventions

Section index: [conventions/](conventions/index.md)

* [Companion package](conventions/companion-package.md) - Opt-in display companion; own providers; not a Fabricate domain.
* [Dependency direction](conventions/dependency-direction.md) - Do not put display/framebuffers back into framework core.
* [Component subtree packaging](conventions/component-subtree-packaging.md) - `tubes/*` split packages + umbrella replace. (`draft`)

# Traps

Section index: [traps/](traps/index.md)

* [Window ≠ IC Panel](traps/window-vs-ic-panel.md) - Do not model IC as a Window backend.
* [Engine ≠ Circuit input](traps/engine-vs-circuit-input.md) - Do not model Circuit as an EngineInput backend. (`draft`)
* [Draw / buffer / output ownership](traps/draw-buffer-output.md) - Renderer must not care which sink presents.
* [Stub shouldClose returns true](traps/stub-should-close.md) - Stub handlers are not event-loop ready. (`draft`)

# Playbooks

Section index: [playbooks/](playbooks/index.md)

* [Path-require from tubes-dev](playbooks/path-require-tubes-dev.md) - Local path-repo symlink checkout under tubes-dev.
