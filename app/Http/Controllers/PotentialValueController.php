<?php

namespace App\Http\Controllers;

use App\Models\PotentialValue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PotentialValueController extends Controller
{
    public function index(): JsonResponse
    {
        $potentialValues = PotentialValue::orderBy('min_value', 'asc')->get();
        return response()->json($potentialValues);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_value' => 'nullable|numeric|min:0',
            'max_value' => 'nullable|numeric|min:0',
            'color' => 'required|string|max:255'
        ]);

        // Validate that max_value is greater than min_value if both are provided
        if ($validated['min_value'] && $validated['max_value'] && $validated['max_value'] <= $validated['min_value']) {
            return response()->json([
                'error' => 'Maximum value must be greater than minimum value'
            ], 422);
        }

        $potentialValue = PotentialValue::create($validated);
        
        return response()->json($potentialValue, 201);
    }

    public function show(PotentialValue $potentialValue): JsonResponse
    {
        return response()->json($potentialValue);
    }

    public function update(Request $request, PotentialValue $potentialValue): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_value' => 'nullable|numeric|min:0',
            'max_value' => 'nullable|numeric|min:0',
            'color' => 'required|string|max:255'
        ]);

        // Validate that max_value is greater than min_value if both are provided
        if ($validated['min_value'] && $validated['max_value'] && $validated['max_value'] <= $validated['min_value']) {
            return response()->json([
                'error' => 'Maximum value must be greater than minimum value'
            ], 422);
        }

        $potentialValue->update($validated);
        
        return response()->json($potentialValue);
    }

    public function destroy(PotentialValue $potentialValue): JsonResponse
    {
        $potentialValue->delete();
        
        return response()->json(['message' => 'Potential value deleted successfully']);
    }
}