<?php

namespace ScrapyardIO\Tubes\Contracts\Framebuffers;

use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\FramebufferDriver;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\FramebufferKind;
use ScrapyardIO\Tubes\Framebuffers\PendingFramebuffer;

interface BufferFactory
{
    /**
     * Start a fluent build for a registered driver (managed or deferred).
     *
     * @param  FramebufferDriver|non-empty-string|null  $driver  Null uses the configured default.
     */
    public function driver(FramebufferDriver|string|null $driver = null): PendingFramebuffer;

    /**
     * Start a fluent build that must resolve to a managed registration.
     *
     * @param  FramebufferDriver|non-empty-string  $driver
     */
    public function managed(FramebufferDriver|string $driver): PendingFramebuffer;

    /**
     * Start a fluent build that must resolve to a deferred registration.
     *
     * @param  non-empty-string  $driver
     */
    public function deferred(string $driver): PendingFramebuffer;

    /**
     * Convenience: built-in full managed strategy.
     */
    public function full(): PendingFramebuffer;

    /**
     * Convenience: built-in dirty-regions managed strategy.
     */
    public function dirty(): PendingFramebuffer;

    /**
     * Convenience: built-in page-segment managed strategy.
     */
    public function page(): PendingFramebuffer;

    /**
     * Start a fluent build for the default (or named) driver from config.
     *
     * @param  non-empty-string|null  $driver
     */
    public function make(?string $driver = null): PendingFramebuffer;

    /**
     * Register a managed framebuffer creator.
     *
     * Callables receive the completed {@see PendingFramebuffer}.
     * Class-strings must implement {@see ManagedFramebuffer} and expose `::sized()`.
     *
     * @param  non-empty-string  $name
     * @param  class-string<ManagedFramebuffer>|callable(PendingFramebuffer): Framebuffer  $creator
     */
    public function extendManaged(string $name, callable|string $creator): static;

    /**
     * Register a deferred framebuffer creator (e.g. SDL/GL-backed).
     *
     * Class-strings must implement {@see DeferredFramebuffer} and expose `::sized()`.
     *
     * @param  non-empty-string  $name
     * @param  class-string|callable(PendingFramebuffer): Framebuffer  $creator
     */
    public function extendDeferred(string $name, callable|string $creator): static;

    /**
     * @return array<int, string>
     */
    public function listFramebuffers(?FramebufferKind $kind = null): array;

    /**
     * Which lane a registration lives in, or null if unknown.
     *
     * @param  FramebufferDriver|non-empty-string  $driver
     */
    public function kindOf(FramebufferDriver|string $driver): ?FramebufferKind;
}
