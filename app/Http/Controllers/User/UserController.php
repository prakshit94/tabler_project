<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get paginated user list.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'search']);
        $users = $this->userService->getPaginatedUsers($filters, $request->input('per_page', 15));

        return response()->json(['data' => $users]);
    }

    /**
     * Get single user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => $user->load('roles', 'permissions')]);
    }

    /**
     * Create user.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|string|unique:users,mobile',
            'password' => 'required|string|min:8',
            'roles' => 'nullable|array',
            'status' => 'nullable|in:active,suspended,blocked',
        ]);

        $user = $this->userService->createUser($validated);

        return response()->json(['message' => 'User created successfully', 'data' => $user], 201);
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'mobile' => 'sometimes|string|unique:users,mobile,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'roles' => 'nullable|array',
            'status' => 'sometimes|in:active,suspended,blocked',
        ]);

        $user = $this->userService->updateUser($user, $validated);

        return response()->json(['message' => 'User updated successfully', 'data' => $user]);
    }

    /**
     * Delete user.
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->deleteUser($user);

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Assign role to user.
     */
    public function assignRole(Request $request, User $user): JsonResponse
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);
        $this->userService->assignRole($user, $request->role);

        return response()->json(['message' => 'Role assigned successfully']);
    }

    /**
     * Remove role from user.
     */
    public function removeRole(Request $request, User $user): JsonResponse
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);
        $this->userService->removeRole($user, $request->role);

        return response()->json(['message' => 'Role removed successfully']);
    }
}
