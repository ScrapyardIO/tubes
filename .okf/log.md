# Directory Update Log

## 2026-08-09

* **Update (draft)**: [TubesServiceProvider](core/tubes-service-provider.md) — `AboutCommand::add('Drivers', …)` contributes Windows / Framebuffers / Fonts from `tubes.defaults.*`.
* **Update (draft)**: [CanvasWindowDemo sketch](core/metal-canvas-sketch.md) — opens via `Window::profile('canvas-window-demo')` / `OpenWindowNode` profile key; CLI overrides optional. tubes-dev `config/tubes.php` metal profiles at **1024×768**, default window driver `metal`.
* **Creation (draft)**: [Tubes config](core/tubes-config.md) — hydrated `config/tubes.php` with MagicAlias `defaults` + `canvas_profiles.windows|panels`; `Window::profile()`; `driver()` null defaults; Workshop publish tag `tubes-config`. Amended [Window factory](core/window-factory.md), [Framebuffer factory](core/framebuffer-factory.md), [Fonts](core/fonts.md), [TubesServiceProvider](core/tubes-service-provider.md), AGENTS.
* **Rename (draft)**: [CanvasWindowDemo sketch](core/metal-canvas-sketch.md) — `#[Sketch('canvas-window-demo')]` (was MetalCanvas / `metal-canvas`); window preset slug `metal-canvas` remains in tubes config.
* **Move (draft)**: [MetalCanvas sketch](core/metal-canvas-sketch.md) — relocated from tubes-dev `app/Runner/Sketches` into `Core/Runner/Sketches` + `Rendering/SoftRenderer2D`; registered from [TubesServiceProvider](core/tubes-service-provider.md). Soft GFX via string FQCNs + `class_exists`.
* **Update (draft)**: [InputHandler](core/input-handler.md) matrix — Metal binding `mtl_input_*` (0.7.3+) + metal-gfx `MetalInputHandler` fan-out in `MetalWindowHandler::pollNative`.
* **Creation (draft)**: HumanInput Phase A skeleton — [Input model (Engine | Circuit)](orientation/input-model.md), trap [Engine ≠ Circuit input](traps/engine-vs-circuit-input.md), [HumanInput](core/human-input.md), [InputHandler](core/input-handler.md) + engine support matrix. Hosts `EngineInput` / hollow `CircuitInput`; devices + controls are mediums (not HumanInput subclasses); companion `Inputs\InputHandler`. Pest `tests/HumanInput/HumanInputTest.php`.

* **Update (draft)**: [Fonts / FontManager](core/fonts.md) — Workshop `make:font` (+ optional `--from=` Adafruit `.h` via `AdafruitGfxHeader`); `tubes/fonts` requires `fabricate/console`. Autopen waves complete: Adafruit Free* gaps, U8g2 stub fills (+ HelvB/Logisoso), OneOffs yAdvance parity. tubes-dev **MetalCanvas** HUD uses `DrawsText` + `helvb-12` / `logisoso-16` (enabled) for v/a/fps/color; speed-mapped ball accent.
* **Creation (draft)**: [Fonts / FontManager](core/fonts.md) — `GFXFont` in contracts, `ClassicFont` + `FontManager` / MagicAlias `Font`, companions `Font::extend`; `DrawingAPI` text + `DrawsText` concern; autopen 0.7 plugs in. Amended [Rendering](core/rendering.md), packaging.
* **Update (draft)**: [Window loop Flow](core/window-loop-flow.md) — tubes-dev `MetalCanvas` uses `MetalCanvasFlow` (AsyncFlow): `BallPhysicsNode` + `PaintTickNode`; paint via `Renderer2D`.
* **Docs**: Ecosystem **Rendering** page for tubes `0.7.x` (`DrawingAPI` / `Renderer2D` / companion stubs); homepage for `tubes/rendering` now resolves.
* **Creation (draft)**: [Rendering / Renderer2D](core/rendering.md) — `DrawingAPI` contract, `Renderer` set/unset framebuffer by reference, `Renderer2D` defaults throw until gfx overrides; subtree `tubes/rendering` + umbrella replace. Amended [draw-buffer-output](traps/draw-buffer-output.md), [component-subtree-packaging](conventions/component-subtree-packaging.md). Canvas wire-up deferred.
* **Update (draft)**: [install:gfx Flow](core/gfx-install-flow.md) — Darwin CUDA disabled; sdl3/open-gl/vulkan require native libs; prompt labels for incompatible vs already installed; split `EnsurePhpExtensionNode` + `EnsureExtensionWrapperNode` + gfx require.
* **Creation (draft)**: [Window loop Flow](core/window-loop-flow.md) — `WindowLoopFlow` open → paint/present/poll → close; `paint` callback is the rendering-engine evolution hook (tubes-dev `MetalCanvas` sketch).
* **Verify (sdl3-gfx / ogx)**: `SDL3WindowHandler` + `OpenGLWindowHandler` match contract — FormatSpec at construct, `WindowManager::extend`, `open()` throws until `bootNative`. Tubes OKF trap [stub shouldClose](traps/stub-should-close.md); window-factory slug list includes `cuda`. Ecosystem sdl3-gfx / ogx pages refreshed for window registry.
* **Verify (vulkan-gfx)**: Companion `VulkanWindowHandler` matches contract — FormatSpec = `rgbaSpec()`, `WindowManager::extend('vulkan')`, `open()` throws until `bootNative`. Ecosystem tubes **Windows** page live; vulkan-gfx seeders refreshed.
* **Fix (draft)**: [TubesServiceProvider](core/tubes-service-provider.md) — bind `InstallGfxCommand` / `UninstallGfxCommand` as container singletons so `ContainerCommandLoader` surfaces them on `workshop list`.
* **Verify (metal-gfx)**: Companion `MetalWindowHandler` matches contract — FormatSpec at construct, `WindowManager::extend('metal')`, `OSWindow` wrap, `open()` throws until `bootNative`. ScrapyardIO tubes/windows + metal-gfx seeders refreshed.

* **Creation (draft)**: [Window factory](core/window-factory.md) + [WindowsServiceProvider](core/windows-service-provider.md) — MagicAlias `Window`, `WindowManager` / `PendingWindow`, config registry, gfx `extend('sdl3'|…)` + `tubes-windows-*` publish tags. Amended [TubesServiceProvider](core/tubes-service-provider.md), [WindowHandler](core/window-handler.md), [Package](orientation/package.md).
* **Creation (draft)**: [Canvas](core/canvas.md) + [WindowHandler](core/window-handler.md) — OSWindow wraps companion handler; FormatSpec at construct; native present bypasses PHP flush. Amended [output model](orientation/output-model.md).
* **Update (draft)**: [Framebuffer factory](core/framebuffer-factory.md) — config registry, `make()`, extension guards, publish tags; TubesServiceProvider registers `install:gfx` / `uninstall:gfx` Flow commands.
* **Update (draft)**: Three abstracts — store-agnostic [Framebuffer](core/framebuffer.md), [Managed](core/managed-framebuffers.md), new [Deferred framebuffer](core/deferred-framebuffer.md) with `present()` / `isHeadless()`; [OpenGL](core/opengl-framebuffer.md) is an empty marker.
* **Creation (draft)**: [OpenGL framebuffer contract](core/opengl-framebuffer.md) — `OpenGLFramebuffer extends DeferredFramebuffer` with `present()` / `isHeadless()`; concrete in `microscrap/ogx`.

## 2026-08-08

* **Update (draft)**: [Framebuffer factory](core/framebuffer-factory.md) companion example — `microscrap/sdl3-gfx` uses `extendDeferred('sdl3', …)` (SDL-owned Deferred; default headless soft surface).
* **Update (draft)**: Subtree-split packaging for **components** — `tubes/contracts` + `tubes/framebuffers` each have `composer.json` / `LICENSE.md` / `.gitattributes`; umbrella `replace` includes them. Convention: [Component subtree packaging](conventions/component-subtree-packaging.md).
* **Update (draft)**: Umbrella packaging — root `.gitattributes` export-ignore (tests/phpunit/.okf/AGENTS/…), MIT `LICENSE`, `composer.json` branch-alias `0.7.x-dev`, homepage → ecosystem `0.7.x`.
* **Creation (draft)**: [Framebuffer factory](core/framebuffer-factory.md) — MagicAlias `Framebuffer` (tubes/Core), `FramebufferManager` + `PendingFramebuffer` fluent build, `extendManaged` / `extendDeferred`, built-ins full/dirty/page. Wired in [FramebuffersServiceProvider](core/framebuffers-service-provider.md); Composer `extra.scrapyard-io.aliases`.
* **Update (draft)**: [Managed framebuffers](core/managed-framebuffers.md) — FullFramebuffer, DirtyRegionsBuffer, PageSegmentBuffer over PixelStore; FormatSpecFramebuffer absorbed into ManagedFramebuffer; DumpedBuffer (string payload).
* **Update (draft)**: [Framebuffer](core/framebuffer.md) — 0.6 base API adapted to PixelStore; contract includes draw/blit/dump/flush(FormatSpec)/damage; slim DamageGranularity without Rect snap yet.
* **Update (draft)**: [PixelStore](core/pixel-store.md) — mutations: `clear`/`fill`/`getPixel`/`setPixel`/`setPixels`/`setSegment` writing packed host bytes (mono/planar/row-major incl. B12).
* **Update (draft)**: [PixelStore](core/pixel-store.md) — init allocates packed `string` from host `FormatSpec::bytesForSurface()` × `z` (default 1); contract accessors + layered `dump()`. Fixed `FormatSpec` `ChannelPalette` import; added `bytesForSurface()`.
* **Initialization**: Created OKF v0.2 bundle for `scrapyard-io/tubes` **0.7.0** (reconstituting tree).
* **Creation (draft)**: Orientation — [Package (0.7)](orientation/package.md), [Ownership vs framework](orientation/ownership-vs-framework.md), [Output model (Window | IC Panel)](orientation/output-model.md).
* **Creation (draft)**: Core — [TubesServiceProvider](core/tubes-service-provider.md), [FramebuffersServiceProvider](core/framebuffers-service-provider.md) (on-disk stubs only).
* **Creation (draft)**: Conventions — [Companion package](conventions/companion-package.md), [Dependency direction](conventions/dependency-direction.md).
* **Creation (draft)**: Traps — [Window ≠ IC Panel](traps/window-vs-ic-panel.md), [Draw / buffer / output ownership](traps/draw-buffer-output.md).
* **Creation (draft)**: Playbook — [Path-require from tubes-dev](playbooks/path-require-tubes-dev.md).
* **Note**: Architecture concepts are product-owner decisions; all concepts `status: draft` pending Angel human verification. No invented 0.6 panel/window APIs.
