<?php

namespace ScrapyardIO\Tubes\Contracts\Windows;

use ScrapyardIO\Tubes\Windows\PendingWindow;

interface WindowFactory
{
    /**
     * Start a fluent build for the default (or named) window driver.
     *
     * @param  non-empty-string|null  $driver  Null uses {@see defaultDriver()}.
     */
    public function driver(?string $driver = null): PendingWindow;

    /**
     * Start a fluent build for the default (or named) window driver.
     *
     * @param  non-empty-string|null  $driver
     */
    public function make(?string $driver = null): PendingWindow;

    /**
     * Hydrate a PendingWindow from tubes.canvas_profiles.windows.{name}.
     *
     * @param  non-empty-string  $name  Profile slug or dotted config path
     */
    public function profile(string $name): PendingWindow;

    /**
     * Register a window handler creator.
     *
     * Class-strings must extend WindowHandler with ctor (string $title, int $width, int $height).
     *
     * @param  non-empty-string  $name
     * @param  class-string|\Closure(\ScrapyardIO\Tubes\Windows\PendingWindow): \ScrapyardIO\Tubes\Windows\WindowHandler  $creator
     */
    public function extend(string $name, callable|string $creator): static;

    /**
     * @return list<string>
     */
    public function listWindows(): array;

    public function defaultDriver(): string;
}
