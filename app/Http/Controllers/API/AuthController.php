<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Throwable;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|unique:users|email',
                'username' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role_id' => 'required|numeric|exists:roles,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }
            $roleId = (int)$request->role_id;

            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role_id' => $roleId,
            ]);
            // Assign role to the user
            $role = Role::find($roleId);
            $user->assignRole($role);


            $token = $user->createToken('authToken')->plainTextToken;

            DB::commit();

            return response()->json(['user' => $user, 'token' => $token], 201);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Server Error',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login a user and return a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('username', 'password');

            $user = User::where('username', $credentials['username'])->first();

            // Check if user exists and password is correct
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['user' => $user, 'token' => $token], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server Error',
                'errors' => $e->getMessage() . "- Line -" . $e->getLine()
            ], 500);
        }
    }

    /**
     * Log the user out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
