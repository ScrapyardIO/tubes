<?php

namespace Tubes\Contracts\Windows;

use Tubes\Windows\Enums\ViewType;

interface WindowSurface extends CanOwnMenuBars
{
    public function close(): void;
    public function present(): void;
    public function isClosed(): bool;
    public function getPointer(): int;
    public function getCurrentWidth(): int;
    public function getCurrentHeight(): int;
    public function getContentPointer(): ?int;
    public function setContentPointer(int $content_pointer): static;

    /**
     * @param  array<string, mixed>  $addl_params
     */
    public function addView(
        string $name,
        ViewType $view_component_enum,
        int $x,
        int $y,
        int $h,
        int $w,
        array $addl_params = []
    ): static;

    /**
     * @param  array<string, mixed>  $addl_params
     */
    public function addLabel(
        string $name,
        int $x,
        int $y,
        int $h,
        int $w,
        array $addl_params = []
    ): static;

    /**
     * @param  array<string, mixed>  $addl_params
     */
    public function addButton(
        string $name,
        int $x,
        int $y,
        int $h,
        int $w,
        array $addl_params = []
    ): static;

    /**
     * @param  array<string, mixed>  $addl_params
     */
    public function addEntry(
        string $name,
        int $x,
        int $y,
        int $h,
        int $w,
        array $addl_params = []
    ): static;

    /**
     * @param  array<string, mixed>  $addl_params
     */
    public function addCheckbox(
        string $name,
        int $x,
        int $y,
        int $h,
        int $w,
        array $addl_params = []
    ): static;

    public function pollClick(string $name): bool;

    public function setLabelText(string $name, string $text): static;

    public function getEntryText(string $name): string;

    public function setEntryText(string $name, string $text): static;

    public function isCheckboxChecked(string $name): bool;

    public function setCheckboxChecked(string $name, bool $checked): static;

    /**
     * @param  array<int, string>  $buttons
     */
    public function showAlert(string $message, string $detail = '', array $buttons = ['OK']): static;

    public function pollAlert(): ?int;
}
