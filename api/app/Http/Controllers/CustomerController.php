<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(private readonly CustomerService $customerService) {}

    public function index(): JsonResponse
    {
        return response()->json($this->customerService->index());
    }

    public function show(Customer $customer): JsonResponse
    {
        return response()->json($this->customerService->show($customer));
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        return response()->json(
            $this->customerService->store($request->validated(), $request->user()),
            201,
        );
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        return response()->json($this->customerService->update($customer, $request->validated()));
    }
}
