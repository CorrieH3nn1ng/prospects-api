<?php

namespace App\Http\Controllers;

use App\Models\ClientMood;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientMoodController extends Controller
{
    public function index(): JsonResponse
    {
        $clientMoods = ClientMood::orderBy('value', 'asc')->get();
        return response()->json($clientMoods);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'emoji' => 'required|string|max:10',
            'value' => 'required|integer|min:1|max:10',
            'color' => 'required|string|max:255'
        ]);

        $clientMood = ClientMood::create($validated);
        
        return response()->json($clientMood, 201);
    }

    public function show(ClientMood $clientMood): JsonResponse
    {
        return response()->json($clientMood);
    }

    public function update(Request $request, ClientMood $clientMood): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'emoji' => 'required|string|max:10',
            'value' => 'required|integer|min:1|max:10',
            'color' => 'required|string|max:255'
        ]);

        $clientMood->update($validated);
        
        return response()->json($clientMood);
    }

    public function destroy(ClientMood $clientMood): JsonResponse
    {
        $clientMood->delete();
        
        return response()->json(['message' => 'Client mood deleted successfully']);
    }
}
