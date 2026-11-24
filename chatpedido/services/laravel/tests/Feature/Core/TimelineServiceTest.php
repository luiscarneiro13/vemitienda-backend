<?php

namespace Tests\Feature\Core;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Core\Settings\Models\Setting;
use App\Core\Timeline\Models\TimelineEntry;
use App\Core\Timeline\TimelineService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TimelineServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_log_creates_an_entry_tied_to_the_active_company_and_user(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['current_company_id' => $company->id]);
        $this->actingAs($user);

        $entry = app(TimelineService::class)->log('customer.created', 'Luis creó un cliente');

        $this->assertDatabaseHas('timeline_entries', [
            'id' => $entry->id,
            'company_id' => $company->id,
            'user_id' => $user->id,
            'action' => 'customer.created',
            'description' => 'Luis creó un cliente',
        ]);
    }

    public function test_log_can_associate_a_subject_model(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['current_company_id' => $company->id]);
        $this->actingAs($user);

        $entry = app(TimelineService::class)->log('company.updated', 'Se actualizó la empresa', $company);

        $this->assertSame($company->getMorphClass(), $entry->subject_type);
        $this->assertSame($company->id, $entry->subject_id);
    }

    public function test_super_admin_action_on_company_resource_is_attributed_to_resource_company(): void
    {
        Role::findOrCreate('super_admin', 'web');

        $adminCompany = Company::factory()->create();
        $targetCompany = Company::factory()->create();
        $targetBranch = Branch::factory()->create(['company_id' => $targetCompany->id]);

        $superAdmin = User::factory()->create(['current_company_id' => $adminCompany->id]);
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin);

        $targetCompany->update(['name' => 'Empresa A Renombrada']);
        $targetBranch->update(['name' => 'Sucursal Norte']);

        $companyEntry = TimelineEntry::withoutTenancy()
            ->where('action', 'company.updated')
            ->where('subject_id', $targetCompany->id)
            ->latest('id')
            ->firstOrFail();

        $branchEntry = TimelineEntry::withoutTenancy()
            ->where('action', 'branch.updated')
            ->where('subject_id', $targetBranch->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame($targetCompany->id, $companyEntry->company_id);
        $this->assertSame($superAdmin->id, $companyEntry->user_id);
        $this->assertSame($targetCompany->id, $branchEntry->company_id);
        $this->assertSame($superAdmin->id, $branchEntry->user_id);
    }

    public function test_global_events_without_subject_or_with_global_subject_keep_null_company(): void
    {
        Role::findOrCreate('super_admin', 'web');

        $adminCompany = Company::factory()->create();
        $superAdmin = User::factory()->create(['current_company_id' => $adminCompany->id]);
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin);

        $subjectless = app(TimelineService::class)->log('system.maintenance', 'Mantenimiento ejecutado');

        $setting = Setting::create([
            'key' => 'app.mode',
            'value' => 'live',
            'type' => 'string',
            'group' => 'general',
        ]);
        $globalSubjectEntry = app(TimelineService::class)->log('setting.updated', 'Modo actualizado', $setting);

        $this->assertNull($subjectless->fresh()->company_id);
        $this->assertSame($superAdmin->id, $subjectless->user_id);
        $this->assertNull($globalSubjectEntry->fresh()->company_id);
    }
}
