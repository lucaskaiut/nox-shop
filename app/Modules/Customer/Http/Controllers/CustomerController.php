<?php

namespace App\Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Customer\Domain\Models\Customer;
use App\Modules\Customer\Domain\Services\CustomerService;
use App\Modules\Customer\Http\Requests\CustomerCreateResetPasswordRequest;
use App\Modules\Customer\Http\Requests\CustomerLoginRequest;
use App\Modules\Customer\Http\Requests\CustomerResetPasswordRequest;
use App\Modules\Customer\Http\Requests\CustomerStoreRequest;
use App\Modules\Customer\Http\Requests\CustomerUpdateRequest;
use App\Modules\Customer\Http\Resources\CustomerCollection;
use App\Modules\Customer\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    private CustomerService $service;
    
    public function __construct(CustomerService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);
        
        return new CustomerCollection($this->service->paginate($request->all()));
    }

    public function store(CustomerStoreRequest $request)
    {
        $this->authorize('create', Customer::class);

        return DB::transaction(function () use ($request) {
            return (new CustomerResource($this->service->create($request->validated())))->response()->setStatusCode(201);
        });
    }

    public function show(int $id)
    {
        $customer = $this->service->findOrFail($id);

        $this->authorize('show', $customer);

        return new CustomerResource($customer);
    }

    public function update(CustomerUpdateRequest $request, int $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $customer = $this->service->findOrFail($id);

            $this->authorize('update', $customer);

            return new CustomerResource($this->service->update($customer, $request->validated()));
        });
    }

    public function destroy(int $id)
    {
        $customer = $this->service->findOrFail($id);

        $this->authorize('delete', $customer);

        $this->service->delete($customer);

        return response()->json([], 204);
    }

    public function register(CustomerStoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            return (new CustomerResource($this->service->register($request->validated())))->response()->setStatusCode(201);
        });
    }

    public function login(CustomerLoginRequest $request)
    {
        return DB::transaction(function () use ($request) {
            return (new CustomerResource($this->service->login($request->validated())));
        });
    }
    
    public function sendResetPasswordLink(CustomerCreateResetPasswordRequest $request)
    {
        DB::transaction(function () use ($request) {
            $this->service->createPasswordReset(collect($request->validated())->get('email'));
        });
    }

    public function resetPassword(CustomerResetPasswordRequest $request)
    {
        DB::transaction(function () use ($request) {
            $this->service->resetPassword($request->validated());
        });
    }
}
