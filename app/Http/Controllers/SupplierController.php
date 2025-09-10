<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Supplier::query();
        
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $suppliers = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json($suppliers);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:suppliers,code|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'status' => 'in:active,inactive,suspended',
            'services_offered' => 'nullable|json',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $supplier = Supplier::create($validated);
        
        return response()->json($supplier, 201);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return response()->json($supplier);
    }

    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:suppliers,code,' . $supplier->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'status' => 'in:active,inactive,suspended',
            'services_offered' => 'nullable|json',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $supplier->update($validated);
        
        return response()->json($supplier);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->delete();
        
        return response()->json(['message' => 'Supplier deleted successfully']);
    }
}
