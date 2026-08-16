---
okf_version: "0.2"
---

# scrapyard-io/tubes

PHP library (`0.8.0`) that registers a `WindowManager`, publishes `config/windows.php` as `windows`, and resolves a `WindowableApplication` through mac or linux drivers.

**Prefer** concepts with `status: stable` when present; content is currently `draft`.

# Orientation

* [Package overview](orientation/overview.md) - Composer identity, autoload, and service wiring

# Architecture

* [Window stack](architecture/stack.md) - Manager, drivers, application, and surface types

# Public PHP API

* [WindowManager](api/window-manager.md) - Driver factory and `app()`
* [WindowableApplication](api/windowable-application.md) - Contract and abstract base
* [WindowSurface](api/window-surface.md) - Contract and abstract base
* [CanOwnMenuBars](api/can-own-menu-bars.md) - Menu ownership and `menuAddItem`
* [Drivers](api/drivers.md) - OS, mac, and linux driver types
* [Exceptions](api/exceptions.md) - `OSApplicationException` and `WindowableException`
* [OSWindow](api/os-window.md) - Magic alias for `window`
* [Config](api/config.md) - `config/windows.php`

# Conventions

* [Service registration](conventions/service-registration.md) - Providers, singleton, publish tag

# Indexes

* [Orientation](orientation/)
* [Architecture](architecture/)
* [API](api/)
* [Conventions](conventions/)
