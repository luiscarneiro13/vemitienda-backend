<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use App\Domain\Catalog\Services\CategoryIconSuggester;
use Filament\Forms\Components\Field;

class CategoryIconPicker extends Field
{
    protected string $view = 'filament.forms.components.category-icon-picker';

    /**
     * Devuelve el mapa de palabras clave a íconos.
     *
     * @return array<string, list<string>>
     */
    public function getKeywordMap(): array
    {
        return CategoryIconSuggester::keywordMap();
    }

    /**
     * Devuelve los íconos agrupados por sección.
     *
     * @return array<string, list<string>>
     */
    public function getGroupedIcons(): array
    {
        return CategoryIconSuggester::groupedIcons();
    }
}
