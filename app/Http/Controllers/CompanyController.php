<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Only app admins can list all companies
        if (!$user->isAppAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = Company::with(['branches', 'users']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('subscription_plan')) {
            $query->where('subscription_plan', $request->subscription_plan);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('business_registration', 'like', "%{$search}%");
            });
        }

        $companies = $query->paginate($request->get('per_page', 15));

        return response()->json($companies);
    }

    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Only app admins can create companies
        if (!$user->isAppAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'website' => 'nullable|string|max:255',
            'business_registration' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'subscription_plan' => 'required|in:basic,standard,premium,enterprise',
            'subscription_starts_at' => 'nullable|date',
            'subscription_ends_at' => 'nullable|date|after_or_equal:subscription_starts_at',
            'monthly_fee' => 'nullable|numeric|min:0',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $company = Company::create($request->all());

        return response()->json($company->load(['branches', 'users']), 201);
    }

    public function show(string $id): JsonResponse
    {
        $user = auth()->user();
        $company = Company::with(['branches', 'users'])->find($id);

        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        // Access control - users can only view their own company unless they're app admin
        if (!$user->isAppAdmin() && $company->id !== $user->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($company);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = auth()->user();
        $company = Company::find($id);

        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        // Access control
        if (!$user->isAppAdmin()) {
            // Company admins can update their own company but with restrictions
            if ($user->isCompanyAdmin() && $company->id === $user->company_id) {
                // Company admins have limited update permissions
                $allowedFields = ['phone', 'address', 'website', 'settings'];
                $updateData = $request->only($allowedFields);
            } else {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } else {
            // App admins can update everything
            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'email' => 'string|email|max:255|unique:companies,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'website' => 'nullable|string|max:255',
                'business_registration' => 'nullable|string|max:255',
                'tax_number' => 'nullable|string|max:255',
                'subscription_plan' => 'in:basic,standard,premium,enterprise',
                'status' => 'in:active,suspended,cancelled',
                'subscription_starts_at' => 'nullable|date',
                'subscription_ends_at' => 'nullable|date|after_or_equal:subscription_starts_at',
                'monthly_fee' => 'nullable|numeric|min:0',
                'settings' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $updateData = $request->all();
        }

        $company->update($updateData);

        return response()->json($company->load(['branches', 'users']));
    }

    public function destroy(string $id): JsonResponse
    {
        $user = auth()->user();

        // Only app admins can delete companies
        if (!$user->isAppAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $company = Company::find($id);

        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        // Check if company has users or branches
        if ($company->users()->count() > 0) {
            return response()->json(['error' => 'Cannot delete company with existing users'], 400);
        }

        if ($company->branches()->count() > 0) {
            return response()->json(['error' => 'Cannot delete company with existing branches'], 400);
        }

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }

    public function stats(string $id): JsonResponse
    {
        $user = auth()->user();
        $company = Company::find($id);

        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        // Access control
        if (!$user->isAppAdmin() && $company->id !== $user->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_users' => $company->users()->count(),
            'active_users' => $company->users()->where('status', 'active')->count(),
            'total_branches' => $company->branches()->count(),
            'active_branches' => $company->branches()->where('status', 'active')->count(),
            'company_admins' => $company->admins()->count(),
            'subscription_status' => $company->isSubscriptionActive() ? 'active' : 'inactive',
        ];

        return response()->json($stats);
    }
}
