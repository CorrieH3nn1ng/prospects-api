<?php

namespace App\Http\Controllers;

use App\Models\Call;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CallController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Call::with(['client']);
        
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('call_type')) {
            $query->where('call_type', $request->call_type);
        }
        
        $calls = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json($calls);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'user_id' => 'required|exists:users,id',
            'client_id' => 'required|exists:clients,id',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'contact_designation' => 'nullable|string|max:255',
            'type_of_business' => 'nullable|string|max:255',
            'call_type_id' => 'nullable|string',
            'area_of_work' => 'nullable|string|max:255',
            'services' => 'nullable|array',
            'followup_date' => 'nullable|date',
            'has_drc_office' => 'boolean',
            'client_interest_level' => 'nullable|integer|min:1|max:5',
            'client_mood' => 'nullable|integer|min:1|max:5',
            'potential_value_id' => 'nullable|exists:potential_values,id',
            'client_satisfaction_level' => 'nullable|integer|min:1|max:5',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,follow_up',
            'scheduled_date' => 'required|date',
            'actions_required' => 'nullable|array',
            'opportunities' => 'nullable|array',
            'routes_challenges' => 'nullable|array',
            'call_notes' => 'nullable|array',
            'documents' => 'nullable|array',
            'inco_terms' => 'nullable|string|max:255'
        ]);

        $call = Call::create($validated);
        
        return response()->json($call, 201);
    }

    public function show(Call $call): JsonResponse
    {
        return response()->json($call->load('client'));
    }

    public function update(Request $request, Call $call): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'user_id' => 'required|exists:users,id',
            'client_id' => 'required|exists:clients,id',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'contact_designation' => 'nullable|string|max:255',
            'type_of_business' => 'nullable|string|max:255',
            'call_type_id' => 'nullable|string',
            'area_of_work' => 'nullable|string|max:255',
            'services' => 'nullable|array',
            'followup_date' => 'nullable|date',
            'has_drc_office' => 'boolean',
            'client_interest_level' => 'nullable|integer|min:1|max:5',
            'client_mood' => 'nullable|integer|min:1|max:5',
            'potential_value_id' => 'nullable|exists:potential_values,id',
            'client_satisfaction_level' => 'nullable|integer|min:1|max:5',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,follow_up',
            'scheduled_date' => 'required|date',
            'actions_required' => 'nullable|array',
            'opportunities' => 'nullable|array',
            'routes_challenges' => 'nullable|array',
            'call_notes' => 'nullable|array',
            'documents' => 'nullable|array',
            'inco_terms' => 'nullable|string|max:255'
        ]);

        $call->update($validated);
        
        return response()->json($call);
    }

    public function destroy(Call $call): JsonResponse
    {
        $call->delete();
        
        return response()->json(['message' => 'Call deleted successfully']);
    }
}
