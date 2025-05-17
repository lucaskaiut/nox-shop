<?php

namespace App\Modules\Company\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Company\Domain\Services\CompanyService;
use App\Modules\Company\Http\Requests\CompanyStoreRequest;
use App\Modules\Company\Http\Requests\CompanyUpdateRequest;
use App\Modules\Company\Http\Resources\CompanyCollection;
use App\Modules\Company\Http\Resources\CompanyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    private CompanyService $service;
    
    public function __construct(CompanyService $service)
    {
        $this->service = $service;
    }

    public function show()
    {
        $company = app('company')->company();

        $this->authorize('show', $company);

        return new CompanyResource($company);
    }

    public function update(CompanyUpdateRequest $request)
    {
        $company = app('company')->company();

        return DB::transaction(function () use ($request, $company) {
            $this->authorize('update', $company);

            return new CompanyResource($this->service->update($company, $request->validated()));
        });
    }
}
