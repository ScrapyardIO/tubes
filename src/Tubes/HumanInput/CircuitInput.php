<?php

namespace ScrapyardIO\Tubes\HumanInput;

/**
 * Circuit / GPIO human-input host — hollow sibling of {@see EngineInput}.
 *
 * Device wiring lands later; do not model Circuit as an EngineInput backend.
 */
abstract class CircuitInput extends HumanInput
{
}
