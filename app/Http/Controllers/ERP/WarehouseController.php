<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = Warehouse::query();

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $warehouses = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();

        if ($request->ajax()) {
            return view('erp.warehouses._table', compact('warehouses', 'view'))->render();
        }

        return view('erp.warehouses.index', compact('warehouses', 'view'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code',
            'state' => 'nullable|string|max:100',
            'is_default' => 'boolean',
        ]);

        if (!empty($validated['is_default'])) {
            Warehouse::where('is_default', true)->update(['is_default' => false]);
        }

        Warehouse::create($validated);

        return redirect()->back()->with('success', 'Warehouse created successfully');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code,' . $warehouse->id,
            'state' => 'nullable|string|max:100',
            'is_default' => 'boolean',
        ]);

        if (!empty($validated['is_default'])) {
            Warehouse::where('is_default', true)->where('id', '!=', $warehouse->id)->update(['is_default' => false]);
        }

        $warehouse->update($validated);

        return redirect()->back()->with('success', 'Warehouse updated successfully');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->back()->with('success', 'Warehouse moved to trash');
    }

    public function restore($id)
    {
        $warehouse = Warehouse::onlyTrashed()->findOrFail($id);
        $warehouse->restore();
        return redirect()->back()->with('success', 'Warehouse restored successfully');
    }

    public function forceDelete($id)
    {
        $warehouse = Warehouse::onlyTrashed()->findOrFail($id);
        $warehouse->forceDelete();
        return redirect()->back()->with('success', 'Warehouse permanently deleted');
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
                Warehouse::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected warehouses moved to trash';
                break;
            case 'restore':
                Warehouse::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected warehouses restored';
                break;
            case 'force-delete':
                Warehouse::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected warehouses permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
