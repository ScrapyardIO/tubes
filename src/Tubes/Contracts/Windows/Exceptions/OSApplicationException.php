<?php

namespace Tubes\Contracts\Windows\Exceptions;

use Exception;

class OSApplicationException extends Exception
{
    public static function windowAlreadyCreated(string $name): static
    {
        return new static("Window $name already exists.");
    }

    public static function windowNotCreated(string $name): static
    {
        return new static("Window $name does not exist.");
    }
}
