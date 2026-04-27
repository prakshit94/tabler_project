<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RoleWebController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $view = $request->input('view', 'active');

        $query = Role::query()->with('permissions');

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $roles = $query->paginate(10)->withQueryString();
        $permissions = Permission::all();

        if ($request->ajax()) {
            return view('admin.roles._table', compact('roles', 'view'))->render();
        }

        return view('admin.roles.index', compact('roles', 'permissions', 'view'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array'
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->back()->with('success', 'Role moved to trash');
    }

    public function restore($id)
    {
        $role = Role::onlyTrashed()->findOrFail($id);
        $role->restore();
        return redirect()->back()->with('success', 'Role restored successfully');
    }

    public function forceDelete($id)
    {
        $role = Role::onlyTrashed()->findOrFail($id);
        $role->forceDelete();
        return redirect()->back()->with('success', 'Role permanently deleted');
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No items selected');
        }

        switch ($action) {
            case 'delete':
                Role::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected roles moved to trash';
                break;
            case 'restore':
                Role::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected roles restored';
                break;
            case 'force-delete':
                Role::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected roles permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
