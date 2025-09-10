<?php

namespace App\Http\Controllers;

use App\Models\ClientSatisfactionLevel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientSatisfactionLevelController extends Controller
{
    public function index(): JsonResponse
    {
        $satisfactionLevels = ClientSatisfactionLevel::orderBy('value', 'asc')->get();
        return response()->json($satisfactionLevels);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|integer|min:1|max:10',
            'color' => 'required|string|max:255'
        ]);

        $satisfactionLevel = ClientSatisfactionLevel::create($validated);
        
        return response()->json($satisfactionLevel, 201);
    }

    public function show(ClientSatisfactionLevel $clientSatisfactionLevel): JsonResponse
    {
        return response()->json($clientSatisfactionLevel);
    }

    public function update(Request $request, ClientSatisfactionLevel $clientSatisfactionLevel): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|integer|min:1|max:10',
            'color' => 'required|string|max:255'
        ]);

        $clientSatisfactionLevel->update($validated);
        
        return response()->json($clientSatisfactionLevel);
    }

    public function destroy(ClientSatisfactionLevel $clientSatisfactionLevel): JsonResponse
    {
        $clientSatisfactionLevel->delete();
        
        return response()->json(['message' => 'Client satisfaction level deleted successfully']);
    }
}