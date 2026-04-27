<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Get all roles.
     */
    public function index(): JsonResponse
    {
        return response()->json(['data' => Role::with('permissions')->get()]);
    }

    /**
     * Create a role.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json(['message' => 'Role created successfully', 'data' => $role], 201);
    }

    /**
     * Get a specific role.
     */
    public function show(Role $role): JsonResponse
    {
        return response()->json(['data' => $role->load('permissions')]);
    }

    /**
     * Update a role.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json(['message' => 'Role updated successfully', 'data' => $role]);
    }

    /**
     * Delete a role.
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }

    /**
     * Assign permissions to a role.
     */
    public function assignPermissions(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($request->permissions);

        return response()->json(['message' => 'Permissions assigned successfully', 'data' => $role->load('permissions')]);
    }
}
