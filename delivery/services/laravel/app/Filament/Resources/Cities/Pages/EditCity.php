<?php

namespace App\Filament\Resources\Cities\Pages;

use App\Domain\Locations\Models\City;
use App\Filament\Resources\Cities\CityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Schema;

class EditCity extends EditRecord
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn (City $record): bool => Schema::hasColumn('branches', 'city_id')
                    && $record->branches()->withoutTenancy()->exists()),
        ];
    }
}
