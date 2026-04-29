<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\IrrigationType;
use Illuminate\Http\Request;

class IrrigationTypeController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = IrrigationType::query();

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $irrigation_types = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();

        if ($request->ajax()) {
            return view('erp.irrigation_types._table', compact('irrigation_types', 'view'))->render();
        }

        return view('erp.irrigation_types.index', compact('irrigation_types', 'view'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:irrigation_types,name',
            'is_active' => 'boolean',
        ]);

        IrrigationType::create($validated);

        return redirect()->back()->with('success', 'Irrigation Type created successfully');
    }

    public function update(Request $request, IrrigationType $irrigationType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:irrigation_types,name,' . $irrigationType->id,
            'is_active' => 'boolean',
        ]);

        $irrigationType->update($validated);

        return redirect()->back()->with('success', 'Irrigation Type updated successfully');
    }

    public function destroy(IrrigationType $irrigationType)
    {
        $irrigationType->delete();
        return redirect()->back()->with('success', 'Irrigation Type moved to trash');
    }

    public function restore($id)
    {
        $irrigationType = IrrigationType::onlyTrashed()->findOrFail($id);
        $irrigationType->restore();
        return redirect()->back()->with('success', 'Irrigation Type restored successfully');
    }

    public function forceDelete($id)
    {
        $irrigationType = IrrigationType::onlyTrashed()->findOrFail($id);
        $irrigationType->forceDelete();
        return redirect()->back()->with('success', 'Irrigation Type permanently deleted');
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
                IrrigationType::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected items moved to trash';
                break;
            case 'restore':
                IrrigationType::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected items restored';
                break;
            case 'force-delete':
                IrrigationType::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected items permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
