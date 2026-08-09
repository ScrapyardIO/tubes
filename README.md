## ScrapyardIO Tubes

> **Note:** This repository is the ScrapyardIO **display companion** (`scrapyard-io/tubes`). It is not a full application skeleton — pair it with [`scrapyard-io/framework`](https://github.com/ScrapyardIO/framework) (or a ScrapyardIO app) and, when you need a GPU/OS surface, a Microscrap `*-gfx` package.

Tubes owns the draw → framebuffer → present pipeline for ScrapyardIO **0.7**: Managed and Deferred framebuffers, OS windows, fonts, and `Renderer2D`. Namespace: `ScrapyardIO\Tubes\`.

```bash
composer require scrapyard-io/tubes:^0.7.0
php workshop vendor:publish --tag=tubes-config
```

Smoke-test a window driver after `install:gfx`:

```bash
./runner canvas-window-demo
```

### Official Documentation

Documentation for Tubes lives on the [ScrapyardIO website](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/overview):

- [Overview](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/overview)
- [Installation](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/installation)
- [Canvas Window Demo](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/canvas-window-demo)
- [Framebuffers](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/framebuffers)
- [Windows](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/windows)
- [Rendering](https://scrapyard-io.projectsaturnstudios.com/ecosystem/scrapyard-io/tubes/0.7.x/rendering)

### Contributing

Thank you for considering contributing to Tubes! Please open issues and pull requests on [GitHub](https://github.com/scrapyard-io/tubes).

### License

Tubes is open-sourced software licensed under the [MIT license](LICENSE).
