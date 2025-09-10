<?php

namespace App\Http\Controllers;

use App\Models\Transporter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransporterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Transporter::query();
        
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('transport_type')) {
            $query->where('transport_type', $request->transport_type);
        }
        
        $transporters = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json($transporters);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:transporters,code|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'transport_type' => 'required|in:road,rail,air,sea,multimodal',
            'vehicle_capacity' => 'nullable|numeric|min:0',
            'capacity_unit' => 'nullable|string|in:tons,cubic_meters,pallets',
            'coverage_area' => 'nullable|string',
            'licensing_info' => 'nullable|json',
            'status' => 'in:active,inactive,suspended',
            'services_offered' => 'nullable|json',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $transporter = Transporter::create($validated);
        
        return response()->json($transporter, 201);
    }

    public function show(Transporter $transporter): JsonResponse
    {
        return response()->json($transporter);
    }

    public function update(Request $request, Transporter $transporter): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:transporters,code,' . $transporter->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'transport_type' => 'required|in:road,rail,air,sea,multimodal',
            'vehicle_capacity' => 'nullable|numeric|min:0',
            'capacity_unit' => 'nullable|string|in:tons,cubic_meters,pallets',
            'coverage_area' => 'nullable|string',
            'licensing_info' => 'nullable|json',
            'status' => 'in:active,inactive,suspended',
            'services_offered' => 'nullable|json',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $transporter->update($validated);
        
        return response()->json($transporter);
    }

    public function destroy(Transporter $transporter): JsonResponse
    {
        $transporter->delete();
        
        return response()->json(['message' => 'Transporter deleted successfully']);
    }
}
