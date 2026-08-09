<?php

namespace ScrapyardIO\Tubes\Contracts\Fonts;

interface FontFactory
{
    /**
     * Register a GFXFont class under a slug (companions: Font::extend / addFont).
     *
     * @param  class-string<GFXFont>  $class
     */
    public function extend(string $name, string $class): static;

    /**
     * @param  class-string<GFXFont>  $class
     */
    public function addFont(string $name, string $class): static;

    /**
     * Resolve a registered font (null uses {@see defaultFont()}).
     */
    public function font(?string $name = null): GFXFont;

    /**
     * Alias of {@see font()} for MagicAlias symmetry with Window/Framebuffer.
     */
    public function driver(?string $name = null): GFXFont;

    public function defaultFont(): string;

    /**
     * Alias of {@see defaultFont()}.
     */
    public function defaultDriver(): string;

    public function hasFont(string $name): bool;

    /**
     * @return array<string, class-string<GFXFont>>
     */
    public function listFonts(): array;
}
