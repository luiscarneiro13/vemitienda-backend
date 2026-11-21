<?php

namespace App\Http\Controllers\PublicCatalog;

use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Services\PublicCatalogCache;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class StoreDirectoryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $search = trim($filters['q'] ?? '');
        $category = $filters['category'] ?? '';
        $page = (int) ($filters['page'] ?? 1);
        $version = PublicCatalogCache::directoryVersion();

        $categories = Cache::remember(
            "public_directory:categories:v{$version}",
            now()->addMinutes(15),
            fn () => $this->publicStores()
                ->whereNotNull('store_category')
                ->where('store_category', '!=', '')
                ->distinct()
                ->orderBy('store_category')
                ->pluck('store_category')
        );

        $storesCacheKey = 'public_directory:stores:v'.$version.':'.sha1(json_encode([$search, $category, $page]));
        $stores = Cache::remember(
            $storesCacheKey,
            now()->addMinutes(10),
            fn () => $this->publicStores()
                ->select(['id', 'name', 'slug', 'store_category', 'store_description', 'cover_image_url'])
                ->with(['branches' => fn ($query) => $query->withoutTenancy()
                    ->select(['id', 'company_id', 'uuid', 'name', 'city_id'])
                    ->with('cityRecord:id,name')])
                ->when($search !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('store_category', 'like', '%'.$search.'%')
                    ->orWhere('store_description', 'like', '%'.$search.'%')))
                ->when($category !== '', fn (Builder $query) => $query->where('store_category', $category))
                ->orderBy('name')->orderBy('id')
                ->paginate(12)
                ->appends(array_filter(['q' => $search, 'category' => $category], fn ($value) => $value !== ''))
                ->fragment('tiendas')
        );

        return view('store-directory', compact('stores', 'categories', 'search', 'category'));
    }

    private function publicStores(): Builder
    {
        return Company::query()
            ->where('is_active', true)
            ->where('status', 'active')
            // No elegir arbitrariamente una sucursal de registros históricos.
            ->whereHas('branches', fn (Builder $query) => $query->withoutTenancy(), '=', 1)
            ->whereHas('branches', fn (Builder $query) => $query->withoutTenancy()
                ->where('status', 'active')
                ->whereNotNull('uuid')
                ->whereNotNull('whatsapp_number')
                ->where('whatsapp_number', '!=', ''))
            ->whereHas('defaultCatalog', fn (Builder $query) => $query->withoutTenancy()->where('is_active', true));
    }
}
