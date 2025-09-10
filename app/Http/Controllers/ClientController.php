<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Client::query();
        
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $clients = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json($clients);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:clients,code|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'employee_count' => 'nullable|integer|min:0',
            'annual_revenue' => 'nullable|numeric|min:0',
            'primary_contact_name' => 'nullable|string|max:255',
            'primary_contact_email' => 'nullable|email|max:255',
            'primary_contact_phone' => 'nullable|string|max:255',
            'primary_contact_designation' => 'nullable|string|max:255',
            'status' => 'in:active,inactive,prospect,customer',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array'
        ]);

        $client = Client::create($validated);
        
        return response()->json($client, 201);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json($client);
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:clients,code,' . $client->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'employee_count' => 'nullable|integer|min:0',
            'annual_revenue' => 'nullable|numeric|min:0',
            'primary_contact_name' => 'nullable|string|max:255',
            'primary_contact_email' => 'nullable|email|max:255',
            'primary_contact_phone' => 'nullable|string|max:255',
            'primary_contact_designation' => 'nullable|string|max:255',
            'status' => 'in:active,inactive,prospect,customer',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array'
        ]);

        $client->update($validated);
        
        return response()->json($client);
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->delete();
        
        return response()->json(['message' => 'Client deleted successfully']);
    }
}
