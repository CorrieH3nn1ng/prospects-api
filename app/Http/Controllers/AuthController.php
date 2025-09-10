<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Middleware is now handled in routes/api.php

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();
        
        // Update last login
        $user->update(['last_login_at' => now()]);

        return $this->respondWithToken($token, $user);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'company_id' => 'nullable|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'role' => 'required|in:app_admin,company_admin,branch_user',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => 'active',
        ]);

        $token = auth()->login($user);

        return $this->respondWithToken($token, $user);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh(), auth()->user());
    }

    public function me(): JsonResponse
    {
        $user = auth()->user()->load(['company', 'branch']);
        
        return response()->json([
            'user' => $user,
        ]);
    }

    protected function respondWithToken($token, $user): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user->load(['company', 'branch']),
        ]);
    }
}
