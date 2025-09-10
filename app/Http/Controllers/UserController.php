<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $query = User::with(['company', 'branch']);

        // Apply access control based on user role
        if ($user->isBranchUser()) {
            // Branch users can only see users in their branch
            $query->where('branch_id', $user->branch_id);
        } elseif ($user->isCompanyAdmin()) {
            // Company admins can see all users in their company
            $query->where('company_id', $user->company_id);
        }
        // App admins can see all users (no filter)

        // Apply filters
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate($request->get('per_page', 15));

        return response()->json($users);
    }

    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'role' => 'required|in:app_admin,company_admin,branch_user',
            'phone' => 'nullable|string|max:20',
            'status' => 'in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Access control checks
        if (!$user->isAppAdmin()) {
            // Non-app admins can only create users in their company
            if ($request->company_id != $user->company_id) {
                return response()->json(['error' => 'Unauthorized to create users in this company'], 403);
            }

            // Only app admins can create other app admins
            if ($request->role === 'app_admin') {
                return response()->json(['error' => 'Unauthorized to create app administrators'], 403);
            }

            // Company admins can't create other company admins unless they're app admin
            if ($request->role === 'company_admin' && !$user->isAppAdmin()) {
                return response()->json(['error' => 'Unauthorized to create company administrators'], 403);
            }
        }

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => $request->status ?? 'active',
        ]);

        return response()->json($newUser->load(['company', 'branch']), 201);
    }

    public function show(string $id): JsonResponse
    {
        $user = auth()->user();
        $targetUser = User::with(['company', 'branch'])->find($id);

        if (!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Access control
        if (!$user->isAppAdmin()) {
            if ($user->isCompanyAdmin() && $targetUser->company_id !== $user->company_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            if ($user->isBranchUser() && $targetUser->branch_id !== $user->branch_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        return response()->json($targetUser);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = auth()->user();
        $targetUser = User::find($id);

        if (!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Access control
        if (!$user->isAppAdmin()) {
            if ($user->isCompanyAdmin() && $targetUser->company_id !== $user->company_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            if ($user->isBranchUser() && ($targetUser->branch_id !== $user->branch_id || $targetUser->id !== $user->id)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'company_id' => 'exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'role' => 'in:app_admin,company_admin,branch_user',
            'phone' => 'nullable|string|max:20',
            'status' => 'in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Additional role-based restrictions
        if (!$user->isAppAdmin() && $request->has('role') && $request->role === 'app_admin') {
            return response()->json(['error' => 'Unauthorized to set app admin role'], 403);
        }

        $updateData = $request->only(['name', 'email', 'company_id', 'branch_id', 'role', 'phone', 'status']);
        
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $targetUser->update($updateData);

        return response()->json($targetUser->load(['company', 'branch']));
    }

    public function updatePassword(Request $request, string $id): JsonResponse
    {
        $user = auth()->user();
        $targetUser = User::find($id);

        if (!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Users can only update their own password
        if ($targetUser->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized to change this password'], 403);
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|same:password'
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $targetUser->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 400);
        }

        // Update password
        $targetUser->update([
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }

    public function destroy(string $id): JsonResponse
    {
        $user = auth()->user();
        $targetUser = User::find($id);

        if (!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Prevent self-deletion
        if ($targetUser->id === $user->id) {
            return response()->json(['error' => 'Cannot delete your own account'], 400);
        }

        // Access control
        if (!$user->isAppAdmin()) {
            if ($user->isCompanyAdmin() && $targetUser->company_id !== $user->company_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            if ($user->isBranchUser()) {
                return response()->json(['error' => 'Unauthorized to delete users'], 403);
            }

            // Company admins can't delete other company admins
            if ($targetUser->role === 'company_admin') {
                return response()->json(['error' => 'Unauthorized to delete company administrators'], 403);
            }
        }

        $targetUser->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
