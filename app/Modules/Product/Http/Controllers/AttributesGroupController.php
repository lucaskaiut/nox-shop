<?php

namespace App\Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Domain\Models\AttributesGroup;
use App\Modules\Product\Domain\Services\AttributesGroupService;
use App\Modules\Product\Http\Requests\AttributesGroupStoreRequest;
use App\Modules\Product\Http\Requests\AttributesGroupUpdateRequest;
use App\Modules\Product\Http\Resources\AttributesGroupCollection;
use App\Modules\Product\Http\Resources\AttributesGroupResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributesGroupController extends Controller
{
    private AttributesGroupService $service;

    public function __construct(AttributesGroupService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return new AttributesGroupCollection($this->service->paginate($request->all()));
    }

    public function store(AttributesGroupStoreRequest $request)
    {
        $this->authorize('create', AttributesGroup::class);

        return DB::transaction(function () use ($request) {
            return (new AttributesGroupResource($this->service->create($request->validated())))->response()->setStatusCode(201);
        });
    }

    public function show(int $id) 
    {
        $attributesGroup = $this->service->findOrFail($id);

        return new AttributesGroupResource($this->service->findOrFail($id));
    }

    public function update(int $id, AttributesGroupUpdateRequest $request)
    {
        return DB::transaction(function () use ($request, $id) {
            $attributesGroup = $this->service->findOrFail($id);

            $this->authorize('update', $attributesGroup);

            return new AttributesGroupResource($this->service->update($attributesGroup, $request->validated()));
        });
    }

    public function destroy(int $id)
    {
        $attributesGroup = $this->service->findOrFail($id);

        $this->authorize('delete', $attributesGroup);

        $this->service->delete($attributesGroup);

        return response()->json([], 204);
    }
}
