<?php

namespace App\Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Customer\Domain\Models\Address;
use App\Modules\Customer\Domain\Models\Customer;
use App\Modules\Customer\Domain\Services\AddressService;
use App\Modules\Customer\Http\Requests\AddressStoreRequest;
use App\Modules\Customer\Http\Requests\AddressUpdateRequest;
use App\Modules\Customer\Http\Resources\AddressCollection;
use App\Modules\Customer\Http\Resources\AddressResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    private AddressService $service;
    
    public function __construct(AddressService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Address::class);

        return new AddressCollection($this->service->paginate($request->all()));
    }

    public function store(AddressStoreRequest $request)
    {
        $this->authorize('create', Address::class);

        return DB::transaction(function () use ($request) {
            return (new AddressResource($this->service->create($request->validated())))->response()->setStatusCode(201);
        });
    }

    public function show(int $id)
    {
        $customer = $this->service->findOrFail($id);

        $this->authorize('show', $customer);

        return new AddressResource($customer);
    }

    public function update(AddressUpdateRequest $request, int $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $customer = $this->service->findOrFail($id);

            $this->authorize('update', $customer);

            return new AddressResource($this->service->update($customer, $request->validated()));
        });
    }

    public function destroy(int $id)
    {
        $customer = $this->service->findOrFail($id);

        $this->authorize('delete', $customer);

        $this->service->delete($customer);

        return response()->json([], 204);
    }
}
