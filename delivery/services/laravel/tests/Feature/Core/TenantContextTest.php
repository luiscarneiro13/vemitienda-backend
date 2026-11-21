<?php

namespace Tests\Feature\Core;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class TenantContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolves_current_company_from_authenticated_user(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['current_company_id' => $company->id]);

        $this->actingAs($user);

        $tenant = app(TenantContext::class);

        $this->assertTrue($tenant->company()->is($company));
        $this->assertSame($company->id, $tenant->companyId());
        $this->assertSame($user->id, $tenant->userId());
    }

    public function test_returns_null_without_authenticated_user(): void
    {
        $tenant = app(TenantContext::class);

        $this->assertNull($tenant->company());
        $this->assertNull($tenant->companyId());
        $this->assertNull($tenant->userId());
    }

    public function test_switch_to_updates_current_company_when_user_belongs_to_it(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $user = User::factory()->create(['current_company_id' => $companyA->id]);
        $user->companies()->attach($companyB);

        $this->actingAs($user);

        app(TenantContext::class)->switchTo($user, $companyB);

        $this->assertSame($companyB->id, $user->refresh()->current_company_id);
    }

    public function test_switch_to_fails_when_user_does_not_belong_to_company(): void
    {
        $company = Company::factory()->create();
        $otherCompany = Company::factory()->create();
        $user = User::factory()->create(['current_company_id' => $company->id]);

        $this->actingAs($user);

        try {
            app(TenantContext::class)->switchTo($user, $otherCompany);
            $this->fail('Se esperaba una HttpException con status 403.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    public function test_switch_to_replaces_branch_with_main_branch_of_new_company(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $branchA = Branch::factory()->create(['company_id' => $companyA->id, 'is_main' => true]);
        $branchB = Branch::factory()->create(['company_id' => $companyB->id, 'is_main' => true]);

        $user = User::factory()->create([
            'current_company_id' => $companyA->id,
            'current_branch_id' => $branchA->id,
        ]);
        $user->companies()->attach([$companyA->id, $companyB->id]);

        $this->actingAs($user);

        app(TenantContext::class)->switchTo($user, $companyB);

        $user->refresh();
        $this->assertSame($companyB->id, $user->current_company_id);
        $this->assertSame($branchB->id, $user->current_branch_id);
    }

    public function test_switch_to_clears_branch_when_new_company_has_no_main_branch(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $branchA = Branch::factory()->create(['company_id' => $companyA->id, 'is_main' => true]);
        Branch::factory()->create(['company_id' => $companyB->id, 'is_main' => false]);

        $user = User::factory()->create([
            'current_company_id' => $companyA->id,
            'current_branch_id' => $branchA->id,
        ]);
        $user->companies()->attach([$companyA->id, $companyB->id]);

        $this->actingAs($user);

        app(TenantContext::class)->switchTo($user, $companyB);

        $user->refresh();
        $this->assertSame($companyB->id, $user->current_company_id);
        $this->assertNull($user->current_branch_id);
    }

    public function test_switch_branch_to_rejects_branch_from_another_company(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $branchA = Branch::factory()->create(['company_id' => $companyA->id]);

        $user = User::factory()->create(['current_company_id' => $companyB->id]);
        $user->companies()->attach([$companyA->id, $companyB->id]);

        $this->actingAs($user);

        try {
            app(TenantContext::class)->switchBranchTo($user, $branchA);
            $this->fail('Se esperaba una HttpException con status 403.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    public function test_switch_branch_to_accepts_branch_of_active_company(): void
    {
        $company = Company::factory()->create();
        $branch = Branch::factory()->create(['company_id' => $company->id]);

        $user = User::factory()->create(['current_company_id' => $company->id]);
        $user->companies()->attach($company->id);

        $this->actingAs($user);

        app(TenantContext::class)->switchBranchTo($user, $branch);

        $this->assertSame($branch->id, $user->refresh()->current_branch_id);
    }
}
