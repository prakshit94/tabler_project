<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\LandUnit;
use Illuminate\Http\Request;

class LandUnitController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = LandUnit::query();

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $land_units = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();

        if ($request->ajax()) {
            return view('erp.land_units._table', compact('land_units', 'view'))->render();
        }

        return view('erp.land_units.index', compact('land_units', 'view'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:land_units,name',
            'code' => 'required|string|max:50|unique:land_units,code',
            'is_active' => 'boolean',
        ]);

        LandUnit::create($validated);

        return redirect()->back()->with('success', 'Land Unit created successfully');
    }

    public function update(Request $request, LandUnit $landUnit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:land_units,name,' . $landUnit->id,
            'code' => 'required|string|max:50|unique:land_units,code,' . $landUnit->id,
            'is_active' => 'boolean',
        ]);

        $landUnit->update($validated);

        return redirect()->back()->with('success', 'Land Unit updated successfully');
    }

    public function destroy(LandUnit $landUnit)
    {
        $landUnit->delete();
        return redirect()->back()->with('success', 'Land Unit moved to trash');
    }

    public function restore($id)
    {
        $landUnit = LandUnit::onlyTrashed()->findOrFail($id);
        $landUnit->restore();
        return redirect()->back()->with('success', 'Land Unit restored successfully');
    }

    public function forceDelete($id)
    {
        $landUnit = LandUnit::onlyTrashed()->findOrFail($id);
        $landUnit->forceDelete();
        return redirect()->back()->with('success', 'Land Unit permanently deleted');
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
                LandUnit::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected items moved to trash';
                break;
            case 'restore':
                LandUnit::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected items restored';
                break;
            case 'force-delete':
                LandUnit::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected items permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
