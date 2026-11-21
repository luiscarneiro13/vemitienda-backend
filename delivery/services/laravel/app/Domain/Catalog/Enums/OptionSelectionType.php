<?php

namespace App\Domain\Catalog\Enums;

enum OptionSelectionType: string
{
    case Single = 'single';
    case Multiple = 'multiple';

    public function label(): string
    {
        return match ($this) {
            self::Single => 'Elegir una',
            self::Multiple => 'Elegir varias',
        };
    }
}
