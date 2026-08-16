---
type: CoreType
title: Drivers
description: OS, mac, and linux window drivers
resource: /src/Tubes/Windows/Drivers/OSWindowDriver.php
tags: [tubes, api]
status: stable
generated: { by: cursor-agent/grok-4.6, at: "2026-08-16T13:39:00Z" }
verified:
    - { by: human:angel@projectsaturnstudios.com, at: "2026-08-16T13:46:00Z"}
sources:
  - id: os-contract
    resource: /src/Tubes/Contracts/Windows/Drivers/OSWindowDriver.php
    title: OSWindowDriver contract
  - id: mac-contract
    resource: /src/Tubes/Contracts/Windows/Drivers/MacOSWindowDriver.php
    title: MacOSWindowDriver
  - id: linux-contract
    resource: /src/Tubes/Contracts/Windows/Drivers/LinuxWindowDriver.php
    title: LinuxWindowDriver
  - id: os-base
    resource: /src/Tubes/Windows/Drivers/OSWindowDriver.php
    title: OSWindowDriver base
  - id: appkit-driver
    resource: /src/Tubes/Windows/Drivers/AppKitWindowDriver.php
    title: AppKitWindowDriver
  - id: gtk-driver
    resource: /src/Tubes/Windows/Drivers/GTKWindowDriver.php
    title: GTKWindowDriver
---

# Contracts

`Tubes\Contracts\Windows\Drivers\OSWindowDriver` declares `application(): WindowableApplication`.[^os-contract]

`MacOSWindowDriver` and `LinuxWindowDriver` extend `OSWindowDriver` with empty bodies.[^mac-contract][^linux-contract]

# Implementations

`Tubes\Windows\Drivers\OSWindowDriver` is abstract, implements the OS contract, holds `protected WindowableApplication $os_app`, and redeclares `application()`.[^os-base]

`AppKitWindowDriver` extends that base and implements `MacOSWindowDriver`. Constructor takes `array $config` and sets `$this->os_app = new AppKitApplication($config['app_name'])`. `application()` returns that instance.[^appkit-driver]

`GTKWindowDriver` extends the same base and implements `LinuxWindowDriver`. Constructor takes `array $config` and sets `$this->os_app = new GTKApplication($config['application_id'], $config['application_flags'])`. `application()` returns that instance.[^gtk-driver]

[^os-contract]: OSWindowDriver contract
[^mac-contract]: MacOSWindowDriver
[^linux-contract]: LinuxWindowDriver
[^os-base]: OSWindowDriver base
[^appkit-driver]: AppKitWindowDriver
[^gtk-driver]: GTKWindowDriver
