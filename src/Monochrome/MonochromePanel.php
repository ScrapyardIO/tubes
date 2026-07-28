<?php

namespace ScrapyardIO\Tubes\Monochrome;

use Fabricate\Displays\EmbeddedDisplay;
use Fabricate\NutsAndBolts\MagicAliases\Circuit;
use Fabricate\Contracts\Displays\DisplayException;
use Fabricate\Contracts\Displays\Interfaces\MonochromeDisplay as DisplayCircuit;

class MonochromePanel extends EmbeddedDisplay
{
    public function __construct(DisplayCircuit $circuit)
    {
        parent::__construct($circuit);
    }

    public static function circuit(string $driver): static
    {
        $circuit = Circuit::driver($driver);
        if($circuit instanceof DisplayCircuit) {
            return new static($circuit);
        }

        $circuit->close();
        throw new DisplayException("Circuit [$driver] is not a Monochrome DisplayPanel.");
    }
}