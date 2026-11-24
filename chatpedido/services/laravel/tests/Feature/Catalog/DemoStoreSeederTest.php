<?php

namespace Tests\Feature\Catalog;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Models\Catalog;
use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductOption;
use App\Domain\Catalog\Models\ProductOptionGroup;
use App\Domain\Locations\Models\City;
use App\Models\User;
use Database\Seeders\CitySeeder;
use Database\Seeders\DemoStoreSeeder;
use Database\Seeders\NavigationDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DemoStoreSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('store_owner', 'web');
    }

    public function test_demo_seeder_is_idempotent(): void
    {
        $this->seed(CitySeeder::class);
        $this->seed(DemoStoreSeeder::class);
        $this->seed(DemoStoreSeeder::class);

        $company = Company::query()->where('slug', 'burger-demo')->first();
        $this->assertNotNull($company);
        $this->assertSame(1, Company::query()->where('slug', 'burger-demo')->count());

        $catalog = Catalog::withoutTenancy()->where('company_id', $company->id)->where('is_default', true)->get();
        $this->assertCount(1, $catalog);

        $this->assertSame(1, Branch::withoutTenancy()->where('company_id', $company->id)->count());
        $this->assertSame(20, CatalogCategory::withoutTenancy()->where('catalog_id', $catalog->first()->id)->count());
        $this->assertSame(38, Product::withoutTenancy()->where('catalog_id', $catalog->first()->id)->count());
        $this->assertSame(24, ProductOptionGroup::withoutTenancy()->whereIn('product_id', Product::withoutTenancy()->where('catalog_id', $catalog->first()->id)->pluck('id'))->count());
        $this->assertSame(86, ProductOption::withoutTenancy()->whereIn('product_option_group_id', ProductOptionGroup::withoutTenancy()->whereIn('product_id', Product::withoutTenancy()->where('catalog_id', $catalog->first()->id)->pluck('id'))->pluck('id'))->count());
        $this->assertSame(20, ProductOptionGroup::withoutTenancy()->where('is_removal', true)->count());

        $owner = User::query()->where('email', 'owner@example.com')->first();
        $this->assertNotNull($owner);
        $this->assertTrue($owner->hasRole('store_owner'));
        $this->assertDatabaseHas('company_user', ['user_id' => $owner->id, 'company_id' => $company->id]);

        // Idempotencia: repetir CitySeeder tampoco duplica
        $this->seed(CitySeeder::class);
        $this->assertSame(4, City::query()->whereIn('slug', ['el-tigre', 'puerto-la-cruz', 'barcelona', 'maturin'])->count());
    }

    public function test_demo_links_open_the_catalog(): void
    {
        $this->withoutVite();
        $this->seed(CitySeeder::class);
        $this->seed(DemoStoreSeeder::class);

        $branches = Branch::withoutTenancy()->whereHas('company', fn ($q) => $q->where('slug', 'burger-demo'))->get();
        $this->assertCount(1, $branches);

        foreach ($branches as $branch) {
            $this->assertNotNull($branch->whatsapp_number);
            $this->get($branch->publicCatalogUrl())->assertOk();
        }
    }

    public function test_navigation_seeder_creates_100_navigable_stores_without_duplicates(): void
    {
        $this->withoutVite();
        $this->seed(NavigationDemoSeeder::class);
        $images = Company::query()->orderBy('id')->pluck('cover_image_url')->all();
        $uuids = Branch::withoutTenancy()->orderBy('id')->pluck('uuid')->all();
        $this->seed(NavigationDemoSeeder::class);

        $this->assertSame(100, Company::query()->count());
        $this->assertSame(100, Branch::withoutTenancy()->count());
        $this->assertSame(100, Catalog::withoutTenancy()->where('is_default', true)->count());
        $this->assertSame(4100, Product::withoutTenancy()->count());
        $this->assertSame(2000, CatalogCategory::withoutTenancy()->count());
        $this->assertSame($images, Company::query()->orderBy('id')->pluck('cover_image_url')->all());
        $this->assertSame($uuids, Branch::withoutTenancy()->orderBy('id')->pluck('uuid')->all());
        $this->assertSame(100, $this->get('/')->assertOk()->viewData('stores')->total());
        $this->assertSame(100, Company::query()->has('branches', '=', 1)->count());

        foreach (Branch::withoutTenancy()->orderBy('id')->get()->nth(20) as $branch) {
            $this->get($branch->publicCatalogUrl())->assertOk()
                ->assertViewHas('payload', fn (array $payload): bool => count($payload['categories']) === 20
                    && count($payload['categories'][0]['products']) === 3);
        }
    }
}
