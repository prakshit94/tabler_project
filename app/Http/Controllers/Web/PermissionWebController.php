<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionWebController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $view = $request->input('view', 'active');

        $query = Permission::query();

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $permissions = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.permissions._table', compact('permissions', 'view'))->render();
        }

        return view('admin.permissions.index', compact('permissions', 'view'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        Permission::create(['name' => $request->name, 'guard_name' => 'web']);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully');
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->back()->with('success', 'Permission moved to trash');
    }

    public function restore($id)
    {
        $permission = Permission::onlyTrashed()->findOrFail($id);
        $permission->restore();
        return redirect()->back()->with('success', 'Permission restored successfully');
    }

    public function forceDelete($id)
    {
        $permission = Permission::onlyTrashed()->findOrFail($id);
        $permission->forceDelete();
        return redirect()->back()->with('success', 'Permission permanently deleted');
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
                Permission::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected permissions moved to trash';
                break;
            case 'restore':
                Permission::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected permissions restored';
                break;
            case 'force-delete':
                Permission::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected permissions permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
