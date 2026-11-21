<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Core\Companies\Models\Company;
use App\Filament\Resources\Companies\CompanyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    /**
     * @var array<string, mixed>
     */
    protected array $branchData = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Company $company */
        $company = $this->record;
        $branch = $company->branches()->withoutTenancy()->first();

        if ($branch) {
            $data['whatsapp_number'] = $branch->whatsapp_number;
            $data['city_id'] = $branch->city_id;
            $data['public_catalog_url'] = $branch->publicCatalogUrl();

            if (blank($data['address'] ?? null) && filled($branch->address)) {
                $data['address'] = $branch->address;
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->branchData = [
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
            'city_id' => $data['city_id'] ?? null,
        ];

        unset($data['whatsapp_number'], $data['city_id'], $data['public_catalog_url']);

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var Company $company */
        $company = $this->record;
        $branchData = $this->branchData;

        $branch = $company->branches()->withoutTenancy()->first();

        if ($branch) {
            $branchUpdates = [];

            if (array_key_exists('whatsapp_number', $branchData) && $branchData['whatsapp_number'] !== null) {
                $branchUpdates['whatsapp_number'] = $branchData['whatsapp_number'];
            }

            if (array_key_exists('city_id', $branchData) && $branchData['city_id'] !== null) {
                $branchUpdates['city_id'] = $branchData['city_id'];
            }

            if (! empty($company->address)) {
                $branchUpdates['address'] = $company->address;
            }

            if (! empty($branchUpdates)) {
                $branch->update($branchUpdates);
            }
        } elseif (! empty($branchData['whatsapp_number']) || ! empty($branchData['city_id'])) {
            $company->branches()->create([
                'name' => 'Sede Principal',
                'whatsapp_number' => $branchData['whatsapp_number'] ?? null,
                'city_id' => $branchData['city_id'] ?? null,
                'address' => $company->address,
                'status' => 'active',
                'is_main' => true,
            ]);
        }
    }
}
