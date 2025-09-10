<?php

namespace App\Http\Controllers;

use App\Models\InterestLevel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InterestLevelController extends Controller
{
    public function index(): JsonResponse
    {
        $interestLevels = InterestLevel::orderBy('value', 'asc')->get();
        return response()->json($interestLevels);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|integer|min:1|max:10',
            'color' => 'required|string|max:255'
        ]);

        $interestLevel = InterestLevel::create($validated);
        
        return response()->json($interestLevel, 201);
    }

    public function show(InterestLevel $interestLevel): JsonResponse
    {
        return response()->json($interestLevel);
    }

    public function update(Request $request, InterestLevel $interestLevel): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|integer|min:1|max:10',
            'color' => 'required|string|max:255'
        ]);

        $interestLevel->update($validated);
        
        return response()->json($interestLevel);
    }

    public function destroy(InterestLevel $interestLevel): JsonResponse
    {
        $interestLevel->delete();
        
        return response()->json(['message' => 'Interest level deleted successfully']);
    }
}
