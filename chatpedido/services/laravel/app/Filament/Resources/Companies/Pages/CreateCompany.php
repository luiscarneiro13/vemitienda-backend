<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Core\Companies\Models\Company;
use App\Filament\Resources\Companies\CompanyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    /**
     * @var array<string, mixed>
     */
    protected array $branchData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->branchData = [
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
            'city_id' => $data['city_id'] ?? null,
        ];

        unset($data['whatsapp_number'], $data['city_id'], $data['public_catalog_url']);

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var Company $company */
        $company = $this->record;
        $branchData = $this->branchData;

        $branch = $company->branches()->withoutTenancy()->first();

        if ($branch) {
            $branchUpdates = [];
            if (! empty($branchData['whatsapp_number'])) {
                $branchUpdates['whatsapp_number'] = $branchData['whatsapp_number'];
            }
            if (! empty($branchData['city_id'])) {
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
