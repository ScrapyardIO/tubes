<?php

namespace ScrapyardIO\Tubes\HumanInput;

class Keyboard
{
    /**
     * @param  array<string, bool>  $keys
     */
    public function __construct(
        protected array $keys = [],
    ) {}

    /**
     * @return array<string, bool>
     */
    public function keys(): array
    {
        return $this->keys;
    }

    public function isDown(string $key): bool
    {
        return ($this->keys[$key] ?? false) === true;
    }

    public function setKey(string $key, bool $down): static
    {
        $this->keys[$key] = $down;

        return $this;
    }
}
