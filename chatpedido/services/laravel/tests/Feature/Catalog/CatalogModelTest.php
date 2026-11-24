<?php

namespace Tests\Feature\Catalog;

use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Models\Catalog;
use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductOption;
use App\Domain\Catalog\Models\ProductOptionGroup;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatalogModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('super_admin', 'web');
    }

    private function loginSuperAdminOf(Company $company): void
    {
        $user = User::factory()->create([
            'company_id' => $company->id,
            'current_company_id' => $company->id,
        ]);
        $user->assignRole('super_admin');

        $this->actingAs($user);
    }

    public function test_category_company_id_is_derived_from_catalog(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $catalogB = Catalog::factory()->create(['company_id' => $companyB->id]);

        $this->loginSuperAdminOf($companyA);

        $category = CatalogCategory::factory()->create(['catalog_id' => $catalogB->id]);

        $this->assertSame($companyB->id, $category->company_id);
    }

    public function test_product_company_id_is_derived_from_catalog(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $catalogB = Catalog::factory()->create(['company_id' => $companyB->id]);
        $categoryB = CatalogCategory::factory()->create(['catalog_id' => $catalogB->id]);

        $this->loginSuperAdminOf($companyA);

        $product = Product::factory()->create([
            'catalog_id' => $catalogB->id,
            'catalog_category_id' => $categoryB->id,
        ]);

        $this->assertSame($companyB->id, $product->company_id);
    }

    public function test_option_group_and_option_company_id_are_derived(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $catalogB = Catalog::factory()->create(['company_id' => $companyB->id]);
        $categoryB = CatalogCategory::factory()->create(['catalog_id' => $catalogB->id]);
        $productB = Product::factory()->create([
            'catalog_id' => $catalogB->id,
            'catalog_category_id' => $categoryB->id,
        ]);

        $this->loginSuperAdminOf($companyA);

        $group = ProductOptionGroup::factory()->create(['product_id' => $productB->id]);
        $option = ProductOption::factory()->create(['product_option_group_id' => $group->id]);

        $this->assertSame($companyB->id, $group->company_id);
        $this->assertSame($companyB->id, $option->company_id);
    }

    public function test_product_with_category_from_other_catalog_is_rejected(): void
    {
        $catalogA = Catalog::factory()->create();
        $catalogB = Catalog::factory()->create();
        $categoryB = CatalogCategory::factory()->create(['catalog_id' => $catalogB->id]);

        $this->expectException(ValidationException::class);

        Product::factory()->create([
            'catalog_id' => $catalogA->id,
            'catalog_category_id' => $categoryB->id,
        ]);
    }

    public function test_single_selection_group_forces_max_one(): void
    {
        $catalog = Catalog::factory()->create();
        $category = CatalogCategory::factory()->create(['catalog_id' => $catalog->id]);
        $product = Product::factory()->create([
            'catalog_id' => $catalog->id,
            'catalog_category_id' => $category->id,
        ]);

        $group = ProductOptionGroup::factory()->create([
            'product_id' => $product->id,
            'selection_type' => 'single',
            'max_selections' => 5,
        ]);

        $this->assertSame(1, $group->max_selections);
    }

    public function test_removal_group_is_forced_to_multiple_and_optional(): void
    {
        $catalog = Catalog::factory()->create();
        $category = CatalogCategory::factory()->create(['catalog_id' => $catalog->id]);
        $product = Product::factory()->create([
            'catalog_id' => $catalog->id,
            'catalog_category_id' => $category->id,
        ]);

        $group = ProductOptionGroup::factory()->create([
            'product_id' => $product->id,
            'selection_type' => 'single',
            'is_required' => true,
            'is_removal' => true,
        ]);

        $this->assertSame('multiple', $group->selection_type);
        $this->assertFalse($group->is_required);
        $this->assertTrue($group->is_removal);
    }

    public function test_deleting_catalog_cascades_categories_and_products(): void
    {
        $catalog = Catalog::factory()->create();
        $category = CatalogCategory::factory()->create(['catalog_id' => $catalog->id]);
        Product::factory()->create([
            'catalog_id' => $catalog->id,
            'catalog_category_id' => $category->id,
        ]);

        $catalog->delete();

        $this->assertSame(0, CatalogCategory::withoutTenancy()->where('catalog_id', $catalog->id)->count());
        $this->assertSame(0, Product::withoutTenancy()->where('catalog_id', $catalog->id)->count());
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $catalog = Catalog::factory()->create();
        $category = CatalogCategory::factory()->create(['catalog_id' => $catalog->id]);
        Product::factory()->create([
            'catalog_id' => $catalog->id,
            'catalog_category_id' => $category->id,
        ]);

        $this->expectException(QueryException::class);

        $category->delete();
    }
}
