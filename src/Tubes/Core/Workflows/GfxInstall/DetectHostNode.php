<?php

namespace ScrapyardIO\Tubes\Core\Workflows\GfxInstall;

use Composer\InstalledVersions;
use Fabricate\Sketches\Flow\Node;
use ScrapyardIO\Tubes\Core\Enums\GfxCompanionTarget;

/**
 * Detect OS / arch / native libs / already-installed gfx companions for gating.
 *
 * @phpstan-type HostBag array{
 *     os_family: string,
 *     arch: string,
 *     libs: array<string, bool>,
 *     allowed: list<string>,
 *     denied: array<string, string>,
 *     installed: array<string, string>
 * }
 */
class DetectHostNode extends Node
{
    public function exec(mixed $prepRes): mixed
    {
        return null;
    }

    /**
     * @param  array<string, mixed>  $shared
     */
    public function post(mixed &$shared, mixed $prepRes, mixed $execRes): mixed
    {
        $os = strtolower(PHP_OS_FAMILY);
        $arch = strtolower(php_uname('m'));
        $libs = $this->probeLibs();

        $allowed = [];
        $denied = [];
        $installed = [];

        foreach (GfxCompanionTarget::cases() as $target) {
            $reason = $this->denyReason($target, $os, $arch, $libs);

            if (! is_null($reason)) {
                $denied[$target->value] = $reason;

                continue;
            }

            if ($this->isInstalled($target)) {
                $installed[$target->value] = 'already installed';

                continue;
            }

            $allowed[] = $target->value;
        }

        $shared['host'] = [
            'os_family' => $os,
            'arch' => $arch,
            'libs' => $libs,
            'allowed' => $allowed,
            'denied' => $denied,
            'installed' => $installed,
        ];

        return 'default';
    }

    /**
     * @return array<string, bool>
     */
    protected function probeLibs(): array
    {
        return [
            'sdl3' => $this->hasPkgConfig('sdl3') || $this->hasDylib('libSDL3'),
            'opengl' => $this->hasFramework('OpenGL') || $this->hasPkgConfig('gl') || $this->hasPkgConfig('opengl'),
            'vulkan' => $this->hasPkgConfig('vulkan') || $this->hasDylib('libvulkan'),
            'metal' => PHP_OS_FAMILY === 'Darwin',
            'cuda' => $this->hasCuda(),
        ];
    }

    /**
     * @param  array<string, bool>  $libs
     */
    protected function denyReason(
        GfxCompanionTarget $target,
        string $os,
        string $arch,
        array $libs,
    ): ?string {
        if ($target === GfxCompanionTarget::CUDA && $os === 'darwin') {
            return 'unavailable on Darwin';
        }

        $families = $target->allowedOsFamilies();
        if ($families !== [] && ! in_array($os, $families, true)) {
            return "unavailable on {$os}";
        }

        if ($target->isHollow()) {
            return 'package surface not ready';
        }

        $archs = $target->allowedArchs();
        $normalizedArch = match ($arch) {
            'amd64' => 'x86_64',
            'aarch64' => 'arm64',
            default => $arch,
        };
        $archOk = $archs === []
            || in_array($arch, $archs, true)
            || in_array($normalizedArch, $archs, true);

        if (! $archOk) {
            return "arch [{$arch}] not supported";
        }

        foreach ($target->requiredNativeLibs() as $lib) {
            if (! ($libs[$lib] ?? false)) {
                return $this->missingLibLabel($lib);
            }
        }

        return null;
    }

    protected function missingLibLabel(string $lib): string
    {
        return match ($lib) {
            'sdl3' => 'SDL3 lib not installed',
            'opengl' => 'OpenGL lib not installed',
            'vulkan' => 'Vulkan lib not installed',
            'metal' => 'Metal not available',
            'cuda' => 'CUDA toolkit not installed',
            default => "native library [{$lib}] not detected",
        };
    }

    protected function isInstalled(GfxCompanionTarget $target): bool
    {
        $package = $target->primaryPackageName();

        if ($package === '') {
            return false;
        }

        if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled($package)) {
            return true;
        }

        $provider = $target->serviceProvider();

        return ! is_null($provider) && class_exists($provider);
    }

    protected function hasPkgConfig(string $module): bool
    {
        if (! $this->commandExists('pkg-config')) {
            return false;
        }

        exec('pkg-config --exists '.escapeshellarg($module).' 2>/dev/null', $out, $code);

        return $code === 0;
    }

    protected function hasDylib(string $prefix): bool
    {
        $paths = [
            '/usr/local/lib',
            '/opt/homebrew/lib',
            '/usr/lib',
        ];

        foreach ($paths as $dir) {
            if (! is_dir($dir)) {
                continue;
            }

            $matches = glob($dir.'/'.$prefix.'*') ?: [];
            if ($matches !== []) {
                return true;
            }
        }

        return false;
    }

    protected function hasFramework(string $name): bool
    {
        return is_dir('/System/Library/Frameworks/'.$name.'.framework')
            || is_dir('/Library/Frameworks/'.$name.'.framework');
    }

    protected function hasCuda(): bool
    {
        if ($this->commandExists('nvcc')) {
            exec('nvcc --version 2>/dev/null', $out, $code);
            if ($code === 0 && implode("\n", $out) !== '') {
                return true;
            }
        }

        return $this->hasDylib('libcudart') || is_dir('/usr/local/cuda');
    }

    protected function commandExists(string $binary): bool
    {
        exec('command -v '.escapeshellarg($binary).' 2>/dev/null', $out, $code);

        return $code === 0 && ($out[0] ?? '') !== '';
    }
}
