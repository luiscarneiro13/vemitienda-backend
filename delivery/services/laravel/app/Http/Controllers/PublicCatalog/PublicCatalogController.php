<?php

namespace App\Http\Controllers\PublicCatalog;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Models\CatalogEvent;
use App\Domain\Catalog\Services\CatalogResolver;
use App\Domain\Catalog\Services\PublicCatalogCache;
use App\Domain\Catalog\Services\PublicCatalogPresenter;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class PublicCatalogController extends Controller
{
    public function show(string $companySlug, string $branchUuid, CatalogResolver $resolver, PublicCatalogPresenter $presenter, Request $request): Response
    {
        $dirVersion = PublicCatalogCache::directoryVersion();
        $context = Cache::remember(
            "public_catalog:route:{$companySlug}:{$branchUuid}:v{$dirVersion}",
            now()->addMinutes(15),
            function () use ($companySlug, $branchUuid, $resolver): ?array {
                $company = Company::query()
                    ->where('slug', $companySlug)
                    ->where('is_active', true)
                    ->where('status', 'active')
                    ->first();

                $branch = $company
                    ? Branch::withoutTenancy()
                        ->with('cityRecord:id,name')
                        ->where('company_id', $company->id)
                        ->where('uuid', $branchUuid)
                        ->first()
                    : null;

                $catalog = ($branch && $branch->isPubliclyAvailable()) ? $resolver->forBranch($branch) : null;

                if (! $company || ! $branch || ! $catalog) {
                    return null;
                }

                return [$company, $branch, $catalog];
            }
        );

        if (! $context) {
            return $this->unavailable();
        }

        [$company, $branch, $catalog] = $context;

        $sessionKey = "catalog_viewed_{$branch->id}";
        $ipHash = hash('sha256', $request->ip() ?? '');
        $userAgent = substr($request->userAgent() ?? '', 0, 500);

        if (! session()->has($sessionKey)) {
            session()->put($sessionKey, now()->timestamp);
            $recordView = static function () use ($company, $branch, $catalog, $ipHash, $userAgent): void {
                CatalogEvent::create([
                    'company_id' => $company->id,
                    'branch_id' => $branch->id,
                    'catalog_id' => $catalog->id,
                    'event_type' => 'view',
                    'ip_hash' => $ipHash,
                    'user_agent' => $userAgent,
                    'created_at' => now(),
                ]);
            };

            if (app()->environment('testing')) {
                $recordView();
            } else {
                defer($recordView);
            }
        }

        return response()
            ->view('public-catalog.show', ['payload' => $presenter->present($company, $branch, $catalog)])
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function trackOrder(string $companySlug, string $branchUuid, Request $request): JsonResponse
    {
        $company = Company::query()
            ->where('slug', $companySlug)
            ->where('is_active', true)
            ->where('status', 'active')
            ->first();

        $branch = $company
            ? Branch::withoutTenancy()
                ->where('company_id', $company->id)
                ->where('uuid', $branchUuid)
                ->first()
            : null;

        if (! $branch || ! $branch->isPubliclyAvailable()) {
            return response()->json(['error' => 'not_found'], 404);
        }

        $validated = $request->validate([
            'total_cents' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'items_count' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]);

        $catalog = $company->defaultCatalog;

        CatalogEvent::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'catalog_id' => $catalog?->id,
            'event_type' => 'order_sent',
            'total_cents' => (int) ($validated['total_cents'] ?? 0),
            'items_count' => (int) ($validated['items_count'] ?? 0),
            'ip_hash' => hash('sha256', $request->ip() ?? ''),
            'user_agent' => substr($request->userAgent() ?? '', 0, 500),
            'created_at' => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function invalid(): Response
    {
        return $this->unavailable();
    }

    private function unavailable(): Response
    {
        return response()
            ->view('public-catalog.unavailable', [], 404)
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
