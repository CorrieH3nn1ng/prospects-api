<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $query = Branch::with(['company', 'users']);

        // Apply access control based on user role
        if ($user->isBranchUser()) {
            // Branch users can only see their own branch
            $query->where('id', $user->branch_id);
        } elseif ($user->isCompanyAdmin()) {
            // Company admins can see all branches in their company
            $query->where('company_id', $user->company_id);
        }
        // App admins can see all branches (no filter)

        // Apply additional filters
        if ($request->has('company_id')) {
            $companyId = $request->company_id;
            
            // Ensure user has access to this company
            if (!$user->isAppAdmin() && $companyId != $user->company_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $query->where('company_id', $companyId);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('manager_name', 'like', "%{$search}%");
            });
        }

        $branches = $query->paginate($request->get('per_page', 15));

        return response()->json($branches);
    }

    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches',
            'email' => 'nullable|string|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'manager_name' => 'nullable|string|max:255',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Access control
        if (!$user->isAppAdmin()) {
            // Non-app admins can only create branches in their company
            if ($request->company_id != $user->company_id) {
                return response()->json(['error' => 'Unauthorized to create branches in this company'], 403);
            }

            // Only company admins and app admins can create branches
            if ($user->isBranchUser()) {
                return response()->json(['error' => 'Unauthorized to create branches'], 403);
            }
        }

        // Verify company exists and is active
        $company = Company::find($request->company_id);
        if (!$company || !$company->isActive()) {
            return response()->json(['error' => 'Invalid or inactive company'], 400);
        }

        $branch = Branch::create($request->all());

        return response()->json($branch->load(['company', 'users']), 201);
    }

    public function show(string $id): JsonResponse
    {
        $user = auth()->user();
        $branch = Branch::with(['company', 'users'])->find($id);

        if (!$branch) {
            return response()->json(['error' => 'Branch not found'], 404);
        }

        // Access control
        if (!$user->isAppAdmin()) {
            if ($user->isCompanyAdmin() && $branch->company_id !== $user->company_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            if ($user->isBranchUser() && $branch->id !== $user->branch_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        return response()->json($branch);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = auth()->user();
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['error' => 'Branch not found'], 404);
        }

        // Access control
        if (!$user->isAppAdmin()) {
            if ($user->isCompanyAdmin() && $branch->company_id !== $user->company_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            if ($user->isBranchUser() && $branch->id !== $user->branch_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'exists:companies,id',
            'name' => 'string|max:255',
            'code' => 'string|max:50|unique:branches,code,' . $id,
            'email' => 'nullable|string|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'manager_name' => 'nullable|string|max:255',
            'status' => 'in:active,inactive',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Restrict what branch users can update
        if ($user->isBranchUser()) {
            // Branch users can only update limited fields
            $allowedFields = ['phone', 'address', 'manager_name', 'settings'];
            $updateData = $request->only($allowedFields);
        } else {
            $updateData = $request->all();
            
            // Ensure company_id changes are valid
            if ($request->has('company_id') && !$user->isAppAdmin()) {
                if ($request->company_id != $user->company_id) {
                    return response()->json(['error' => 'Unauthorized to move branch to different company'], 403);
                }
            }
        }

        $branch->update($updateData);

        return response()->json($branch->load(['company', 'users']));
    }

    public function destroy(string $id): JsonResponse
    {
        $user = auth()->user();
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['error' => 'Branch not found'], 404);
        }

        // Access control - only app admins and company admins can delete branches
        if ($user->isBranchUser()) {
            return response()->json(['error' => 'Unauthorized to delete branches'], 403);
        }

        if ($user->isCompanyAdmin() && $branch->company_id !== $user->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if branch has users
        if ($branch->users()->count() > 0) {
            return response()->json(['error' => 'Cannot delete branch with existing users'], 400);
        }

        $branch->delete();

        return response()->json(['message' => 'Branch deleted successfully']);
    }

    public function stats(string $id): JsonResponse
    {
        $user = auth()->user();
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['error' => 'Branch not found'], 404);
        }

        // Access control
        if (!$user->isAppAdmin()) {
            if ($user->isCompanyAdmin() && $branch->company_id !== $user->company_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            if ($user->isBranchUser() && $branch->id !== $user->branch_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $stats = [
            'total_users' => $branch->users()->count(),
            'active_users' => $branch->users()->where('status', 'active')->count(),
            'company' => $branch->company->name,
            'status' => $branch->status,
        ];

        return response()->json($stats);
    }
}
