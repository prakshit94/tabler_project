<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Get all permissions.
     */
    public function index(): JsonResponse
    {
        return response()->json(['data' => Permission::all()]);
    }

    /**
     * Create a permission.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $request->name, 'guard_name' => 'web']);

        return response()->json(['message' => 'Permission created successfully', 'data' => $permission], 201);
    }

    /**
     * Get a specific permission.
     */
    public function show(Permission $permission): JsonResponse
    {
        return response()->json(['data' => $permission]);
    }

    /**
     * Update a permission.
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return response()->json(['message' => 'Permission updated successfully', 'data' => $permission]);
    }

    /**
     * Delete a permission.
     */
    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
