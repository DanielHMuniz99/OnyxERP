<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;

class InventoryController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService) {}

    public function index(): JsonResponse
    {
        return response()->json($this->inventoryService->index());
    }

    public function show(Inventory $inventory): JsonResponse
    {
        return response()->json($this->inventoryService->show($inventory));
    }

    public function store(StoreInventoryRequest $request): JsonResponse
    {
        return response()->json(
            $this->inventoryService->store($request->validated(), $request->user()),
            201,
        );
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory): JsonResponse
    {
        return response()->json($this->inventoryService->update($inventory, $request->validated()));
    }
}
