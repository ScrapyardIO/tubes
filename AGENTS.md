# Agent guidelines — scrapyard-io/tubes

## Knowledge Bundle (OKF)

This package ships an Open Knowledge Format bundle at [`.okf/`](.okf/) (excluded from Composer dist via `.gitattributes` `export-ignore`).

Before changing tubes code or advising on ScrapyardIO display / framebuffer / window / panel architecture **for this package**:

1. Read [`.okf/index.md`](.okf/index.md) first (progressive disclosure).
2. Open only the linked concepts needed for the task.
3. Prefer `status: stable` concepts; treat `deprecated` as historical only. New/changed concepts stay `status: draft` until a human verifies them.
4. When you learn something durable about **this package**, update the affected `.okf` concept(s) and append `.okf/log.md`.
5. Keep the `.okf` bundle at the **package root** only — do not nest extra `.okf` folders under `src/Tubes/*`.
6. Framework, GPIO, circuits, drivers, and native-binding knowledge belongs in those packages’ own docs / `.okf` bundles, not here.

## Package rules (quick) — 0.7.x (reconstituting)

- Composer: `scrapyard-io/tubes` **0.7.0**. PHP `^8.4|^8.5|^8.6`. Namespace `ScrapyardIO\Tubes\` → `src/Tubes`.
- Discovery: `extra.scrapyard-io.providers` → `ScrapyardIO\Tubes\Core\Providers\TubesServiceProvider` (AggregateServiceProvider → FramebuffersServiceProvider).
- MagicAlias: `Framebuffer` / `Window` / `Font` → managers (`extra.scrapyard-io.aliases`); `driver()` with no name uses `tubes.defaults.*`.
- Root config: `config/tubes.php` merged as `tubes`; publish with `workshop vendor:publish --tag=tubes-config`. Canvas presets: `tubes.canvas_profiles.windows|panels`; windows via `Window::profile('slug')`. See `.okf/core/tubes-config.md`.
- Subtree components: each splittable folder under `src/Tubes/{Component}/` **except Core** needs `composer.json` + `LICENSE.md` + `.gitattributes`; umbrella `replace` maps `tubes/{component}` at `self.version` (see `.okf/conventions/component-subtree-packaging.md`). Core stays umbrella-only.
- **Ownership:** tubes owns the ScrapyardIO **display** surface as an opt-in companion — not slim framework core.
- **Output model:** abstract output → sibling concretes **OS Window** | **IC Panel** (not Window-as-IC). Pipeline: draw → framebuffer → present.
- **Input model:** abstract HumanInput → sibling concretes **EngineInput** | **CircuitInput** (not Circuit-as-Engine). Devices are the shared medium. Companion matrix: `.okf/core/input-handler.md`; model: `.okf/orientation/input-model.md`.
- **Package sketches:** live under `Core/Runner/Sketches` with `#[Sketch('…')]`; register from `TubesServiceProvider` via `SketchRegistry::register` (not app convention scan). Demo: `canvas-window-demo` — soft GFX Renderer2D FQCNs + `SoftRenderer2D` fallback. See `.okf/core/metal-canvas-sketch.md`.
- Most folders are empty scaffolds; document only classes that exist on disk. Do not invent 0.6 MonochromePanel/ColorPanel APIs unless PHP files are present.
- Window/embedded path can land before IC Panel (IC/circuits restore later).
- Do not put display/framebuffers back into `scrapyard-io/framework`.
