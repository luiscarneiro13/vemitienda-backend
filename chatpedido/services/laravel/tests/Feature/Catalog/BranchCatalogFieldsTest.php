<?php

namespace Tests\Feature\Catalog;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Domain\Locations\Models\City;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BranchCatalogFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_branch_gets_uuid_on_create(): void
    {
        $branch = Branch::factory()->create();

        $this->assertNotEmpty($branch->uuid);
        $this->assertTrue(Str::isUuid($branch->uuid));
    }

    public function test_two_branches_have_different_uuids(): void
    {
        $a = Branch::factory()->create();
        $b = Branch::factory()->create();

        $this->assertNotSame($a->uuid, $b->uuid);
    }

    public function test_whatsapp_number_is_normalized_to_digits(): void
    {
        $branch = Branch::factory()->create(['whatsapp_number' => '+58 (414) 123-4567']);

        $this->assertSame('584141234567', $branch->whatsapp_number);
    }

    public function test_empty_whatsapp_is_saved_as_null(): void
    {
        $branch = Branch::factory()->create(['whatsapp_number' => '   ']);

        $this->assertNull($branch->whatsapp_number);
    }

    public function test_branch_belongs_to_city(): void
    {
        $city = City::factory()->create();
        $branch = Branch::factory()->create(['city_id' => $city->id]);

        $this->assertTrue($branch->cityRecord->is($city));
    }

    public function test_city_with_branches_cannot_be_deleted(): void
    {
        $city = City::factory()->create();
        Branch::factory()->create(['city_id' => $city->id]);

        $this->expectException(QueryException::class);

        $city->delete();
    }

    public function test_a_second_branch_is_rejected_even_when_the_existing_branch_is_inactive(): void
    {
        $branch = Branch::factory()->create(['status' => 'inactive']);

        $this->expectException(ValidationException::class);
        Branch::factory()->create(['company_id' => $branch->company_id]);
    }

    public function test_moving_a_branch_to_a_company_with_a_branch_is_rejected(): void
    {
        $first = Branch::factory()->create();
        $second = Branch::factory()->create();
        try {
            $second->update(['company_id' => $first->company_id]);
            $this->fail('La reasignación debió rechazarse.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('company_id', $exception->errors());
            $this->assertNotSame($first->company_id, $second->fresh()->company_id);
        }
    }

    public function test_existing_branches_can_be_edited_and_future_limit_can_be_increased(): void
    {
        config(['core.branches_per_company' => 2]);
        $company = Company::factory()->create();
        $branches = Branch::factory()->count(2)->create(['company_id' => $company->id]);
        config(['core.branches_per_company' => 1]);
        $branches->first()->update(['name' => 'Nombre actualizado']);

        $this->assertSame('Nombre actualizado', $branches->first()->fresh()->name);
        $this->assertSame(2, $company->branches()->count());
    }
}
