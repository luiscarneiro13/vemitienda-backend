<?php

namespace App\Filament\Resources\Users\Concerns;

trait SyncsCompanyMembership
{
    protected function syncCompanyMembership(): void
    {
        $user = $this->record;

        if (! $user->company_id) {
            return;
        }

        $user->companies()->syncWithoutDetaching([$user->company_id => ['role' => 'owner']]);

        if (! $user->current_company_id) {
            $user->forceFill(['current_company_id' => $user->company_id])->save();
        }
    }
}
