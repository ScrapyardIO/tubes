<?php

namespace ScrapyardIO\Tubes\Contracts\Fonts;

use Exception;

class FontException extends Exception
{
    public static function invalid(string $message): self
    {
        return new self($message);
    }
}
