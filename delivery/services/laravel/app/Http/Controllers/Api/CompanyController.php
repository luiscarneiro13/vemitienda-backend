<?php

namespace App\Http\Controllers\Api;

use App\Core\Companies\Models\Company;
use App\Core\Companies\Services\CompanyService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(private readonly CompanyService $companies) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless(
            $user && ($user->hasRole('super_admin') || $user->can('companies.view')),
            403
        );

        $query = Company::query();

        if (! $user->hasRole('super_admin')) {
            $query->whereIn('companies.id', $user->companies()->select('companies.id'));
        }

        return response()->json($query->paginate());
    }

    public function show(Request $request, Company $company): JsonResponse
    {
        $user = $request->user();

        abort_unless(
            $user && (
                $user->hasRole('super_admin')
                || ($user->can('companies.view') && $user->companies()->whereKey($company->getKey())->exists())
            ),
            403
        );

        return response()->json($company);
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $company = $this->companies->create($request->validated());

        return response()->json($company, 201);
    }

    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $company = $this->companies->update($company, $request->validated());

        return response()->json($company);
    }
}
