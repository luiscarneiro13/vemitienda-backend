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
use Spatie\Permission\Models\Role;

/**
 * Datos ficticios para probar navegación local. No crea cuentas ni envía mensajes.
 * Ejecutar explícitamente: php artisan db:seed --class=NavigationDemoSeeder
 */
class NavigationDemoSeeder extends Seeder
{
    /**
     * IDs de fotos de Unsplash ya usadas y verificadas en el proyecto.
     *
     * @var list<string>
     */
    private const PHOTOS = [
        'photo-1568901346375-23c9450c58cd', 'photo-1550547660-d9450f859349', 'photo-1561758033-d89a9ad46330',
        'photo-1513104890138-7c749659a591', 'photo-1579751626657-72bc17010498', 'photo-1574071318508-1cdbab80d002',
        'photo-1547592180-85f173990554', 'photo-1546069901-ba9599a7e63c', 'photo-1512621776951-a57141f2eefd',
        'photo-1442512595331-e89e73853f31', 'photo-1488477181946-6428a0291777', 'photo-1509440159596-0249088772ff',
        'photo-1619740455993-9f5a2c344c45', 'photo-1622483767028-3f66f32aef97', 'photo-1565299507177-b0ac66763828',
        'photo-1598155523122-3842334d2c17', 'photo-1600271886742-f049cd451bba',
    ];

    /** @var list<string> 19 categorías genéricas + la del tema = 20 por tienda (sin chocar con categorías de tema). */
    private const GENERIC_CATEGORIES = [
        'Entradas', 'Combos', 'Platos fuertes', 'Bebidas', 'Postres',
        'Meriendas', 'Para compartir', 'Snacks', 'Vegano', 'Infantil',
        'Ensaladas', 'Salsas', 'Acompañantes', 'Milkshakes', 'Café y té',
        'Promociones', 'Tortas', 'Sopas', 'Especiales',
    ];

    public function run(): void
    {
        $this->call(CitySeeder::class);
        $cities = City::query()->whereIn('slug', ['el-tigre', 'puerto-la-cruz', 'barcelona', 'maturin'])
            ->orderBy('slug')->get();
        $names = [
            'Aurora', 'La Esquina', 'Buen Gusto', 'El Patio', 'La Plaza',
            'Los Amigos', 'El Encuentro', 'La Terraza', 'Brisa', 'El Jardín',
            'La Estación', 'El Rincón', 'Sol', 'La Parada', 'La Familia',
            'La Casona', 'Horizonte', 'El Paseo', 'Origen', 'La Colina',
        ];
        $themes = $this->themes();

        foreach ($themes as $themeIndex => $theme) {
            for ($nameIndex = 0; $nameIndex < 5; $nameIndex++) {
                $number = $themeIndex * 5 + $nameIndex + 1;
                $name = $names[($number - 1) % count($names)];
                $city = $cities[($number - 1) % $cities->count()];

                DB::transaction(function () use ($theme, $themeIndex, $name, $number, $city) {
                    $company = Company::query()->firstOrCreate(['slug' => sprintf('navigation-demo-%03d', $number)], [
                        'name' => $theme['prefix'].' '.$name,
                        'store_category' => $theme['category'],
                        'store_description' => $theme['description'],
                        'cover_image_url' => 'https://images.unsplash.com/'.self::PHOTOS[($themeIndex * 3) % count(self::PHOTOS)].'?auto=format&fit=crop&w=760&q=80',
                        'country' => 'VE', 'currency' => 'USD', 'language' => 'es',
                        'timezone' => 'America/Caracas', 'status' => 'active', 'is_active' => true,
                    ]);
                    // Al re-sembrar, los datos demo se alinean con el tema asignado.
                    $company->forceFill([
                        'name' => $theme['prefix'].' '.$name,
                        'store_category' => $theme['category'],
                        'store_description' => $theme['description'],
                        'cover_image_url' => 'https://images.unsplash.com/'.self::PHOTOS[($themeIndex * 3) % count(self::PHOTOS)].'?auto=format&fit=crop&w=760&q=80',
                    ])->save();
                    Branch::withoutTenancy()->firstOrCreate(['company_id' => $company->id], [
                        'name' => 'Centro', 'city_id' => $city->id,
                        'address' => 'Dirección de demostración '.$number,
                        // Número de prueba común a todas las tiendas demo.
                        'whatsapp_number' => '584120816349',
                        'country' => 'VE', 'timezone' => 'America/Caracas',
                        'is_main' => true, 'status' => 'active',
                    ]);
                    $catalog = app(EnsureDefaultCatalogAction::class)->execute($company);
                    $category = CatalogCategory::withoutTenancy()->firstOrNew(
                        ['catalog_id' => $catalog->id, 'name' => $theme['category']],
                        ['company_id' => $catalog->company_id, 'is_active' => true, 'sort_order' => 1]
                    );
                    if (! $category->exists) {
                        $category->setRelation('catalog', $catalog);
                        $category->save();
                    }

                    $this->seedProducts($theme, $themeIndex, $catalog, $category, $number);

                    // 19 categorías genéricas adicionales (20 en total por tienda).
                    foreach (self::GENERIC_CATEGORIES as $catIndex => $catName) {
                        $genericCat = CatalogCategory::withoutTenancy()->firstOrNew(
                            ['catalog_id' => $catalog->id, 'name' => $catName],
                            ['company_id' => $catalog->company_id, 'is_active' => true, 'sort_order' => $catIndex + 2]
                        );
                        if (! $genericCat->exists) {
                            $genericCat->setRelation('catalog', $catalog);
                            $genericCat->save();
                        }

                        $this->seedGenericProducts($theme, $themeIndex, $catalog, $genericCat, $catName, $catIndex, $number);
                    }

                    // Podar categorías que ya no corresponden (p.ej. si cambió el tema del número).
                    $allowed = array_merge([$theme['category']], self::GENERIC_CATEGORIES);
                    CatalogCategory::withoutTenancy()
                        ->where('catalog_id', $catalog->id)
                        ->whereNotIn('name', $allowed)
                        ->get()
                        ->each(function (CatalogCategory $stale) {
                            Product::withoutTenancy()->where('catalog_category_id', $stale->id)->get()->each->delete();
                            $stale->delete();
                        });
                });
            }
        }

        $firstCompany = Company::query()->orderBy('id')->first();
        if ($firstCompany) {
            Role::findOrCreate('store_owner', 'web');
            $owner = User::query()->firstOrCreate(['email' => 'owner@example.com'], [
                'name' => 'Dueño Demo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'company_id' => $firstCompany->id,
                'current_company_id' => $firstCompany->id,
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
        }

        $this->command?->info('100 tiendas de prueba preparadas (20 categorías c/u). Inicio: '.route('home'));
        $this->command?->warn('Datos ficticios e imágenes ilustrativas de internet. Los números de WhatsApp son de prueba.');
    }

    private function themes(): array
    {
        return [
            [
                'prefix' => 'Burger', 'category' => 'Hamburguesas',
                'description' => 'Hamburguesas a la plancha y extras a tu gusto.',
                'products' => [['Hamburguesa clásica', 5.50], ['Hamburguesa doble', 7.50], ['Papas fritas', 2.50]],
                'extras' => [['Queso extra', 0.50], ['Tocineta', 1.00], ['Huevo', 0.75], ['Aguacate', 0.75]],
                'removals' => ['Sin cebolla', 'Sin salsa de tomate', 'Sin pepinillos', 'Sin lechuga'],
            ],
            [
                'prefix' => 'Pizzería', 'category' => 'Pizza',
                'description' => 'Masa artesanal, queso fundido y tus ingredientes favoritos.',
                'products' => [['Pizza margarita', 8], ['Pizza pepperoni', 10], ['Pan de ajo', 3]],
                'extras' => [['Queso extra', 1.00], ['Pepperoni extra', 1.00], ['Champiñones', 0.75]],
                'removals' => ['Sin cebolla', 'Sin pimentón', 'Sin aceitunas', 'Sin orégano'],
            ],
            [
                'prefix' => 'Cocina', 'category' => 'Comida casera',
                'description' => 'Platos preparados con ese sabor que recuerda a casa.',
                'products' => [['Almuerzo de la casa', 6], ['Ensalada fresca', 4], ['Jugo natural', 2]],
                'extras' => [['Porción de arroz', 1.00], ['Tajadas', 0.75], ['Queso rallado', 0.50]],
                'removals' => ['Sin cebolla', 'Sin cilantro', 'Sin ají', 'Sin arroz'],
            ],
            [
                'prefix' => 'Café', 'category' => 'Dulces y café',
                'description' => 'Café recién preparado y algo dulce para acompañarlo.',
                'products' => [['Café con leche', 2], ['Brownie', 3], ['Croissant', 2.50]],
                'extras' => [['Shot extra', 0.50], ['Crema batida', 0.50], ['Leche de almendras', 0.60]],
                'removals' => ['Sin azúcar', 'Sin canela', 'Sin crema'],
            ],
            [
                'prefix' => 'Arepera', 'category' => 'Arepas',
                'description' => 'Desayunos y rellenos para empezar el día con sabor.',
                'products' => [['Arepa de queso', 3], ['Arepa de pollo', 4], ['Jugo de naranja', 2]],
                'extras' => [['Queso extra', 0.50], ['Aguacate', 0.75], ['Carne mechada', 1.00]],
                'removals' => ['Sin cebolla', 'Sin aguacate', 'Sin mantequilla'],
            ],
            [
                'prefix' => 'Dogs', 'category' => 'Perros calientes',
                'description' => 'Perros calientes con todas las salsas y toppings.',
                'products' => [['Perro clásico', 3], ['Perro especial', 4.50], ['Papas de la casa', 2.50]],
                'extras' => [['Queso fundido', 0.75], ['Tocineta', 1.00], ['Papas paja', 0.50]],
                'removals' => ['Sin cebolla', 'Sin chucrut', 'Sin mostaza'],
            ],
            [
                'prefix' => 'Sushi', 'category' => 'Sushi',
                'description' => 'Rolls frescos preparados al momento.',
                'products' => [['Roll california', 9], ['Roll dragón', 11], ['Sopa miso', 3]],
                'extras' => [['Aguacate extra', 1.00], ['Queso crema', 0.75], ['Salsa anguila', 0.50]],
                'removals' => ['Sin ajonjolí', 'Sin aguacate', 'Sin alga'],
            ],
            [
                'prefix' => 'Pollo', 'category' => 'Pollo frito',
                'description' => 'Pollo crujiente, alitas y acompañantes.',
                'products' => [['Pollo frito 2pz', 6], ['Alitas x6', 5], ['Ensalada coleslaw', 4]],
                'extras' => [['Salsa extra', 0.50], ['Papas', 1.00], ['Tajadas', 0.75]],
                'removals' => ['Sin piel', 'Sin salsa', 'Sin hueso'],
            ],
            [
                'prefix' => 'Taquería', 'category' => 'Tacos',
                'description' => 'Tacos, burritos y antojitos al estilo mexicano.',
                'products' => [['Tacos al pastor', 4], ['Burrito grande', 6], ['Nachos', 3.50]],
                'extras' => [['Guacamole', 1.00], ['Queso extra', 0.50], ['Carne extra', 1.50]],
                'removals' => ['Sin cilantro', 'Sin cebolla', 'Sin picante'],
            ],
            [
                'prefix' => 'Empanadas', 'category' => 'Empanadas',
                'description' => 'Empanadas recién fritas y tequeños.',
                'products' => [['Empanada de carne', 2], ['Empanada de queso', 2], ['Tequeños x4', 3]],
                'extras' => [['Salsa de ajo', 0.30], ['Guasacaca', 0.30]],
                'removals' => ['Sin cebolla', 'Sin ají dulce'],
            ],
            [
                'prefix' => 'Parrilla', 'category' => 'Parrilla',
                'description' => 'Cortes a la parrilla y chorizos artesanales.',
                'products' => [['Parrilla mixta', 15], ['Chorizo artesanal', 5], ['Ensalada mixta', 4]],
                'extras' => [['Guasacaca', 0.50], ['Yuca frita', 1.00], ['Queso de mano', 1.00]],
                'removals' => ['Sin grasa', 'Sin chimichurri', 'Sin sal extra'],
            ],
            [
                'prefix' => 'Marisquería', 'category' => 'Mariscos',
                'description' => 'Pescados y mariscos frescos del día.',
                'products' => [['Ceviche', 12], ['Pescado frito', 11], ['Arroz marinero', 10]],
                'extras' => [['Tostones', 1.00], ['Aguacate', 0.75], ['Limón extra', 0]],
                'removals' => ['Sin cilantro', 'Sin cebolla', 'Sin ají'],
            ],
            [
                'prefix' => 'Pastas', 'category' => 'Pasta',
                'description' => 'Pastas caseras con salsas de la casa.',
                'products' => [['Pasta alfredo', 8], ['Lasagna de carne', 9], ['Pan de ajo', 3]],
                'extras' => [['Parmesano extra', 0.75], ['Albóndigas', 1.50], ['Champiñones', 0.75]],
                'removals' => ['Sin ajo', 'Sin perejil', 'Sin queso'],
            ],
            [
                'prefix' => 'Shawarma', 'category' => 'Shawarma',
                'description' => 'Shawarmas y platos árabes.',
                'products' => [['Shawarma de pollo', 5], ['Shawarma de carne', 6], ['Falafel x4', 4]],
                'extras' => [['Hummus', 0.75], ['Pan pita extra', 0.50], ['Salsa de ajo', 0.30]],
                'removals' => ['Sin cebolla', 'Sin pepinillos', 'Sin salsa de ajo'],
            ],
            [
                'prefix' => 'Verde', 'category' => 'Vegetariano',
                'description' => 'Bowls, wraps y opciones 100% vegetales.',
                'products' => [['Bowl veggie', 7], ['Wrap vegano', 6], ['Jugo verde', 3]],
                'extras' => [['Tofu', 1.00], ['Aguacate', 0.75], ['Semillas', 0.40]],
                'removals' => ['Sin nueces', 'Sin cilantro', 'Sin gluten'],
            ],
            [
                'prefix' => 'Brunch', 'category' => 'Desayunos',
                'description' => 'Desayunos completos y café recién hecho.',
                'products' => [['Pancakes x3', 5], ['Huevos rancheros', 6], ['Café americano', 2]],
                'extras' => [['Tocineta', 1.00], ['Miel extra', 0.30], ['Frutas', 0.75]],
                'removals' => ['Sin gluten', 'Sin lactosa', 'Sin azúcar'],
            ],
            [
                'prefix' => 'Heladería', 'category' => 'Helados',
                'description' => 'Helados artesanales y milkshakes.',
                'products' => [['Copa 2 bolas', 4], ['Milkshake', 4], ['Barquilla', 2]],
                'extras' => [['Topping chocolate', 0.50], ['Nueces', 0.40], ['Crema batida', 0.50]],
                'removals' => ['Sin nueces', 'Sin chocolate', 'Sin cono'],
            ],
            [
                'prefix' => 'Panadería', 'category' => 'Panadería',
                'description' => 'Pan artesanal y pastelitos del día.',
                'products' => [['Pan artesanal', 3], ['Cachito de jamón', 1.50], ['Pastelito de queso', 2]],
                'extras' => [['Mantequilla', 0.30], ['Mermelada', 0.40]],
                'removals' => ['Sin ajonjolí', 'Sin mantequilla'],
            ],
            [
                'prefix' => 'Wok', 'category' => 'Comida china',
                'description' => 'Arroces, lumpias y platos al wok.',
                'products' => [['Arroz frito especial', 7], ['Lumpias x4', 4], ['Pollo kung pao', 9]],
                'extras' => [['Camarones extra', 2.00], ['Salsa soya', 0.30], ['Tallarines', 1.00]],
                'removals' => ['Sin MSG', 'Sin cebollín', 'Sin picante'],
            ],
            [
                'prefix' => 'Deli', 'category' => 'Sándwiches',
                'description' => 'Sándwiches, wraps y sopas del día.',
                'products' => [['Club sándwich', 5], ['Wrap de pollo', 5], ['Sopa del día', 3]],
                'extras' => [['Aguacate', 0.75], ['Queso extra', 0.50], ['Tocineta', 1.00]],
                'removals' => ['Sin mayonesa', 'Sin tomate', 'Sin cebolla'],
            ],
        ];
    }

    /** Productos de la categoría del tema (con extras y quitados). */
    private function seedProducts(array $theme, int $themeIndex, Catalog $catalog, CatalogCategory $category, int $number): void
    {
        foreach ($theme['products'] as $index => [$productName, $price]) {
            $imageUrl = 'https://images.unsplash.com/'.self::PHOTOS[($themeIndex * 3 + $index) % count(self::PHOTOS)].'?auto=format&fit=crop&w=700&q=80';
            $product = $this->upsertProduct($catalog, $category, $productName, [
                'description' => 'Preparado al momento. Personaliza tu pedido.',
                'price' => $price,
                'is_active' => true,
                'is_available' => ! ($index === 2 && $number % 7 === 0),
                'sort_order' => $index,
                'image_url' => $imageUrl,
            ]);

            if ($index === 0) {
                $this->upsertOptionGroup($product, 'Presentación', [
                    'selection_type' => 'single',
                    'is_required' => true,
                    'sort_order' => 1,
                ], [
                    ['name' => 'Individual', 'price' => 0],
                    ['name' => 'Para compartir', 'price' => $price],
                ]);

                $extras = array_map(fn (array $item): array => [
                    'name' => $item[0],
                    'price' => $item[1],
                ], $theme['extras']);

                $this->upsertOptionGroup($product, 'Extras', [
                    'selection_type' => 'multiple',
                    'is_required' => false,
                    'max_selections' => 3,
                    'sort_order' => 2,
                ], $extras);
            }

            if ($index < 2) {
                $this->seedRemovals($product, $theme['removals']);
            }
        }
    }

    /** Dos productos por categoría genérica, con su grupo "Sin ingredientes". */
    private function seedGenericProducts(array $theme, int $themeIndex, Catalog $catalog, CatalogCategory $category, string $catName, int $catIndex, int $number): void
    {
        $basePrice = $theme['products'][1][1];
        foreach ([["{$catName} Especial", $basePrice], ["{$catName} Premium", round($basePrice * 1.5, 2)]] as $index => [$productName, $price]) {
            $imageUrl = 'https://images.unsplash.com/'.self::PHOTOS[($themeIndex * 3 + $catIndex + $index) % count(self::PHOTOS)].'?auto=format&fit=crop&w=700&q=80';
            $product = $this->upsertProduct($catalog, $category, $productName, [
                'description' => 'Preparado al momento. Personaliza tu pedido.',
                'price' => $price,
                'is_active' => true,
                'is_available' => ($number + $catIndex + $index) % 9 !== 0,
                'sort_order' => $index,
                'image_url' => $imageUrl,
            ]);

            if ($index === 0) {
                $this->seedRemovals($product, $theme['removals']);
            }
        }
    }

    private function upsertProduct(Catalog $catalog, CatalogCategory $category, string $name, array $attributes): Product
    {
        $product = Product::withoutTenancy()->firstOrNew(
            ['catalog_id' => $catalog->id, 'name' => $name],
            array_merge($attributes, [
                'company_id' => $catalog->company_id,
                'catalog_category_id' => $category->id,
            ])
        );

        if (! $product->exists || $product->image_url !== $attributes['image_url']) {
            $product->image_url = $attributes['image_url'];
            $product->setRelation('catalog', $catalog);
            $product->setRelation('category', $category);
            $product->save();
        }

        return $product;
    }

    private function seedRemovals(Product $product, array $names): void
    {
        $options = array_map(fn (string $optName): array => [
            'name' => $optName,
            'price' => 0,
        ], $names);

        $this->upsertOptionGroup($product, 'Sin ingredientes', [
            'selection_type' => 'multiple',
            'is_required' => false,
            'is_removal' => true,
            'sort_order' => 9,
        ], $options);
    }

    /**
     * @param  list<array{name: string, price: int|float}>  $options
     */
    private function upsertOptionGroup(Product $product, string $name, array $attributes, array $options): void
    {
        $group = ProductOptionGroup::withoutTenancy()->firstOrNew(
            ['product_id' => $product->id, 'name' => $name],
            array_merge($attributes, ['company_id' => $product->company_id])
        );

        if (! $group->exists) {
            $group->setRelation('product', $product);
            $group->save();
        }

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
