<?php

namespace Database\Seeders;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Actions\EnsureDefaultCatalogAction;
use App\Domain\Catalog\Models\Catalog;
use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductOption;
use App\Domain\Catalog\Models\ProductOptionGroup;
use App\Domain\Locations\Models\City;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoStoreSeeder extends Seeder
{
    public function run(): void
    {
        $company = DB::transaction(function (): Company {
            $company = Company::query()->firstOrCreate(['slug' => 'burger-demo'], [
                'name' => 'Burger Demo',
                'currency' => 'USD',
                'country' => 'VE',
                'timezone' => 'America/Caracas',
                'language' => 'es',
                'status' => 'active',
                'is_active' => true,
            ]);
            $catalog = app(EnsureDefaultCatalogAction::class)->execute($company);
            $elTigre = City::query()->where('slug', 'el-tigre')->firstOrFail();

            Branch::withoutTenancy()->firstOrCreate(
                ['company_id' => $company->id],
                [
                    'name' => 'Centro',
                    'city_id' => $elTigre->id,
                    'address' => 'Av. Francisco de Miranda',
                    'whatsapp_number' => '584120816349',
                    'is_main' => true,
                    'status' => 'active',
                    'timezone' => 'America/Caracas',
                    'country' => 'VE',
                ]
            );

            $burgers = $this->category($catalog, 'Hamburguesas', 1);
            $dogs = $this->category($catalog, 'Perros calientes', 2);
            $drinks = $this->category($catalog, 'Bebidas', 3);

            $clasica = $this->product($catalog, $burgers, 'Hamburguesa Clásica', 5.50, true, 'photo-1568901346375-23c9450c58cd');
            $this->group($clasica, 'Tamaño', 'single', true, 1, [
                ['name' => 'Normal', 'price' => 0],
                ['name' => 'Doble', 'price' => 2.00],
            ]);
            $this->group($clasica, 'Extras', 'multiple', false, 2, [
                ['name' => 'Queso', 'price' => 0.50],
                ['name' => 'Tocineta', 'price' => 1.00],
                ['name' => 'Huevo', 'price' => 0.75],
            ], 3);
            $this->removals($clasica, 3, ['Sin cebolla', 'Sin salsa de tomate', 'Sin pepinillos', 'Sin lechuga']);

            $bbq = $this->product($catalog, $burgers, 'Hamburguesa BBQ', 7.00, false, 'photo-1550547660-d9450f859349');
            $this->removals($bbq, 1, ['Sin cebolla', 'Sin pepinillos']);

            $perro = $this->product($catalog, $dogs, 'Perro Especial', 3.00, true, 'photo-1619740455993-9f5a2c344c45');
            $this->group($perro, 'Salsas', 'multiple', false, 1, [
                ['name' => 'Ketchup', 'price' => 0],
                ['name' => 'Mostaza', 'price' => 0],
                ['name' => 'Mayonesa', 'price' => 0],
            ]);
            $this->removals($perro, 2, ['Sin cebolla', 'Sin chucrut']);

            $refresco = $this->product($catalog, $drinks, 'Refresco 1.5L', 2.50, true, 'photo-1622483767028-3f66f32aef97');
            $this->group($refresco, 'Sabor', 'single', true, 1, [
                ['name' => 'Cola', 'price' => 0],
                ['name' => 'Naranja', 'price' => 0],
            ]);

            // 17 categorías extra para completar 20 y probar la navegación del catálogo.
            $extraCategories = [
                'Entradas', 'Combos', 'Platos fuertes', 'Postres', 'Desayunos',
                'Para compartir', 'Snacks', 'Vegetariano', 'Infantil', 'Ensaladas',
                'Salsas', 'Acompañantes', 'Helados', 'Café y té', 'Promociones',
                'Sandwiches', 'Especiales',
            ];
            $photos = [
                'photo-1568901346375-23c9450c58cd', 'photo-1550547660-d9450f859349', 'photo-1619740455993-9f5a2c344c45',
                'photo-1546069901-ba9599a7e63c', 'photo-1512621776951-a57141f2eefd', 'photo-1488477181946-6428a0291777',
                'photo-1565299507177-b0ac66763828', 'photo-1622483767028-3f66f32aef97',
            ];
            foreach ($extraCategories as $catIndex => $catName) {
                $extraCat = $this->category($catalog, $catName, $catIndex + 4);
                foreach ([["{$catName} Especial", 6], ["{$catName} Premium", 9]] as $index => [$productName, $price]) {
                    $extra = $this->product(
                        $catalog, $extraCat, $productName, $price, true,
                        $photos[($catIndex + $index) % count($photos)]
                    );
                    if ($index === 0) {
                        $this->removals($extra, 1, ['Sin cebolla', 'Sin salsa de tomate', 'Sin pepinillos', 'Sin lechuga']);
                    }
                }
            }

            $owner = User::query()->firstOrCreate(['email' => 'owner@example.com'], [
                'name' => 'Dueño Burger Demo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
                'current_company_id' => $company->id,
            ]);
            $owner->syncRoles(['store_owner']);

            $now = now();
            $pivots = Company::query()->pluck('id')->map(fn (int $id): array => [
                'company_id' => $id,
                'user_id' => $owner->id,
                'role' => 'owner',
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            if ($pivots !== []) {
                DB::table('company_user')->insertOrIgnore($pivots);
            }

            return $company;
        });

        Branch::withoutTenancy()->where('company_id', $company->id)->with('company:id,slug')->orderBy('id')->get()
            ->each(fn (Branch $branch) => $this->command?->info("{$branch->name}: {$branch->publicCatalogUrl()}"));
    }

    private function category(Catalog $catalog, string $name, int $sortOrder): CatalogCategory
    {
        $category = CatalogCategory::withoutTenancy()->firstOrNew(
            ['catalog_id' => $catalog->id, 'name' => $name],
            ['company_id' => $catalog->company_id, 'sort_order' => $sortOrder, 'is_active' => true]
        );

        if (! $category->exists) {
            $category->setRelation('catalog', $catalog);
            $category->save();
        }

        return $category;
    }

    private function product(Catalog $catalog, CatalogCategory $category, string $name, float $price, bool $available, ?string $photo = null): Product
    {
        $url = $photo !== null
            ? 'https://images.unsplash.com/'.$photo.'?auto=format&fit=crop&w=700&q=80'
            : null;

        $product = Product::withoutTenancy()->firstOrNew(
            ['catalog_id' => $catalog->id, 'name' => $name],
            [
                'company_id' => $catalog->company_id,
                'catalog_category_id' => $category->id,
                'price' => $price,
                'image_url' => $url,
                'is_active' => true,
                'is_available' => $available,
            ]
        );

        if (! $product->exists || ($url !== null && $product->image_url !== $url)) {
            $product->image_url = $url ?? $product->image_url;
            $product->setRelation('catalog', $catalog);
            $product->setRelation('category', $category);
            $product->save();
        }

        return $product;
    }

    private function group(Product $product, string $name, string $selectionType, bool $required, int $sortOrder, array $options, ?int $maxSelections = null): ProductOptionGroup
    {
        $group = ProductOptionGroup::withoutTenancy()->firstOrNew(
            ['product_id' => $product->id, 'name' => $name],
            [
                'company_id' => $product->company_id,
                'selection_type' => $selectionType,
                'is_required' => $required,
                'max_selections' => $maxSelections,
                'sort_order' => $sortOrder,
            ]
        );

        if (! $group->exists) {
            $group->setRelation('product', $product);
            $group->save();
        }

        $this->syncGroupOptions($group, $options);

        return $group;
    }

    private function removals(Product $product, int $sortOrder, array $names): ProductOptionGroup
    {
        $group = ProductOptionGroup::withoutTenancy()->firstOrNew(
            ['product_id' => $product->id, 'name' => 'Sin ingredientes'],
            [
                'company_id' => $product->company_id,
                'selection_type' => 'multiple',
                'is_required' => false,
                'is_removal' => true,
                'sort_order' => $sortOrder,
            ]
        );

        if (! $group->exists) {
            $group->setRelation('product', $product);
            $group->save();
        }

        $options = array_map(fn (string $name): array => ['name' => $name, 'price' => 0], $names);
        $this->syncGroupOptions($group, $options);

        return $group;
    }

    /**
     * @param  list<array{name: string, price: int|float}>  $options
     */
    private function syncGroupOptions(ProductOptionGroup $group, array $options): void
    {
        $existingNames = $group->wasRecentlyCreated
            ? []
            : ProductOption::withoutTenancy()
                ->where('product_option_group_id', $group->id)
                ->pluck('name')
                ->flip()
                ->all();

        $now = now();
        $rows = [];

        foreach ($options as $index => $option) {
            if (isset($existingNames[$option['name']])) {
                continue;
            }

            $rows[] = [
                'company_id' => $group->company_id,
                'product_option_group_id' => $group->id,
                'name' => $option['name'],
                'price' => $option['price'],
                'is_active' => true,
                'sort_order' => $index,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows !== []) {
            ProductOption::withoutTenancy()->insert($rows);
        }
    }
}
