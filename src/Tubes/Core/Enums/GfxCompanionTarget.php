<?php

namespace ScrapyardIO\Tubes\Core\Enums;

/**
 * Hardcoded gfx companion install matrix for workshop install:gfx.
 */
enum GfxCompanionTarget: string
{
    case SDL3 = 'sdl3';
    case METAL = 'metal';
    case OPEN_GL = 'open-gl';
    case VULKAN = 'vulkan';
    case CUDA = 'cuda';

    public function label(): string
    {
        return match ($this) {
            self::SDL3 => 'SDL3 GFX (microscrap/sdl3-gfx)',
            self::METAL => 'Metal GFX (microscrap/metal-gfx)',
            self::OPEN_GL => 'OpenGL GFX (microscrap/ogx)',
            self::VULKAN => 'Vulkan GFX (microscrap/vulkan-gfx)',
            self::CUDA => 'CUDA GFX (microscrap/cuda-gfx)',
        };
    }

    /**
     * Parent binding / extension-wrapper Composer constraints (install before *-gfx).
     *
     * @return list<string>
     */
    public function bindingPackages(): array
    {
        return match ($this) {
            self::SDL3 => [
                'microscrap/sdl3:^0.7.0',
            ],
            self::METAL => [
                'microscrap/metal:^0.7.0',
            ],
            self::OPEN_GL => [
                'microscrap/glfw:^0.7.0',
                'microscrap/open-gl:^0.7.0',
            ],
            self::VULKAN => [
                'microscrap/glfw:^0.7.0',
                'microscrap/vulkan:^0.7.0',
            ],
            self::CUDA => [
                'microscrap/glfw:^0.7.0',
                'microscrap/cuda:^0.7.0',
            ],
        };
    }

    /**
     * GFX companion Composer constraints (after bindings + PHP extension).
     *
     * @return list<string>
     */
    public function gfxPackages(): array
    {
        return match ($this) {
            self::SDL3 => [
                'microscrap/sdl3-gfx:^0.7.0',
            ],
            self::METAL => [
                'microscrap/metal-gfx:^0.7.0',
            ],
            self::OPEN_GL => [
                'microscrap/ogx:^0.7.0',
            ],
            self::VULKAN => [
                'microscrap/vulkan-gfx:^0.7.0',
            ],
            self::CUDA => [
                'microscrap/cuda-gfx:^0.7.0',
            ],
        };
    }

    /**
     * Full stack in install order (bindings → gfx). Used by uninstall.
     *
     * @return list<string>
     */
    public function composerPackages(): array
    {
        return array_values(array_unique([
            ...$this->bindingPackages(),
            ...$this->gfxPackages(),
        ]));
    }

    /**
     * Packages removed by uninstall:gfx (gfx + bindings stack).
     *
     * @return list<string>
     */
    public function composerPackageNames(): array
    {
        return array_map(
            static fn (string $constraint): string => explode(':', $constraint, 2)[0],
            $this->composerPackages(),
        );
    }

    /**
     * Primary gfx package name for "already installed" detection.
     */
    public function primaryPackageName(): string
    {
        $gfx = $this->gfxPackages()[0] ?? $this->composerPackages()[0] ?? '';

        return explode(':', $gfx, 2)[0];
    }

    public function phpExtension(): ?string
    {
        return match ($this) {
            self::SDL3 => 'sdl3',
            self::METAL => 'metal',
            self::OPEN_GL => 'opengl',
            self::VULKAN => 'vulkan',
            self::CUDA => 'cuda',
        };
    }

    public function piePackage(): ?string
    {
        return match ($this) {
            self::SDL3 => 'php-io-extensions/sdl3',
            self::METAL => 'php-io-extensions/metal',
            self::OPEN_GL => 'php-io-extensions/opengl',
            self::VULKAN => 'php-io-extensions/vulkan',
            self::CUDA => 'php-io-extensions/cuda',
        };
    }

    public function framebufferSlug(): string
    {
        return $this->value;
    }

    public function publishTag(): string
    {
        return 'tubes-framebuffers-'.$this->framebufferSlug();
    }

    public function serviceProvider(): ?string
    {
        return match ($this) {
            self::SDL3 => 'Microscrap\\GFX\\SDL3\\Providers\\SDL3GfxServiceProvider',
            self::METAL => 'Microscrap\\GFX\\Metal\\Providers\\MetalGfxServiceProvider',
            self::OPEN_GL => 'Microscrap\\GFX\\OGX\\Providers\\OgxServiceProvider',
            self::VULKAN => 'Microscrap\\GFX\\Vulkan\\Providers\\VulkanGfxServiceProvider',
            self::CUDA => 'Microscrap\\GFX\\CUDA\\Providers\\CudaGfxServiceProvider',
        };
    }

    /**
     * @return list<string> PHP_OS_FAMILY values (lowercase), empty = any
     */
    public function allowedOsFamilies(): array
    {
        return match ($this) {
            self::METAL => ['darwin'],
            self::CUDA => ['linux'],
            self::SDL3, self::OPEN_GL, self::VULKAN => ['darwin', 'linux'],
        };
    }

    /**
     * @return list<string> php_uname('m') values, empty = any
     */
    public function allowedArchs(): array
    {
        return match ($this) {
            self::METAL => ['arm64', 'aarch64', 'x86_64', 'amd64'],
            default => ['arm64', 'aarch64', 'x86_64', 'amd64'],
        };
    }

    /**
     * Native library probe keys understood by DetectHostNode.
     *
     * @return list<string>
     */
    public function requiredNativeLibs(): array
    {
        return match ($this) {
            self::SDL3 => ['sdl3'],
            self::METAL => ['metal'],
            self::OPEN_GL => ['opengl'],
            self::VULKAN => ['vulkan'],
            self::CUDA => ['cuda'],
        };
    }

    public function isHollow(): bool
    {
        return false;
    }
}
