<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Quote::with(['client']);
        
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        
        $quotes = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json($quotes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'client_id' => 'required|exists:clients,id',
            'quote_number' => 'required|string|unique:quotes,quote_number|max:255',
            'quote_date' => 'required|date',
            'valid_until' => 'required|date|after:quote_date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:draft,sent,pending,accepted,rejected,expired,cancelled',
            'subtotal' => 'required|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'terms_conditions' => 'nullable|string',
            'prepared_by' => 'nullable|string|max:255',
            'sent_date' => 'nullable|date',
            'response_date' => 'nullable|date',
            'client_feedback' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'conversion_probability' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string'
        ]);

        $quote = Quote::create($validated);
        
        return response()->json($quote->load('client'), 201);
    }

    public function show(Quote $quote): JsonResponse
    {
        return response()->json($quote->load('client'));
    }

    public function update(Request $request, Quote $quote): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'client_id' => 'required|exists:clients,id',
            'quote_number' => 'required|string|max:255|unique:quotes,quote_number,' . $quote->id,
            'quote_date' => 'required|date',
            'valid_until' => 'required|date|after:quote_date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:draft,sent,pending,accepted,rejected,expired,cancelled',
            'subtotal' => 'required|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'terms_conditions' => 'nullable|string',
            'prepared_by' => 'nullable|string|max:255',
            'sent_date' => 'nullable|date',
            'response_date' => 'nullable|date',
            'client_feedback' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'conversion_probability' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string'
        ]);

        $quote->update($validated);
        
        return response()->json($quote->load('client'));
    }

    public function destroy(Quote $quote): JsonResponse
    {
        $quote->delete();
        
        return response()->json(['message' => 'Quote deleted successfully']);
    }
}
