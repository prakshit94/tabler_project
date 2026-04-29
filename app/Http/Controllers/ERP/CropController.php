<?php

namespace App\Http\Controllers\ERP;

/*
 * This controller handles CRUD operations for Crops master data.
 * It follows the same pattern as BrandController for consistency.
 */

use App\Http\Controllers\Controller;
use App\Models\Crop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CropController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = Crop::query();

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
        }

        $crops = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();

        if ($request->ajax()) {
            return view('erp.crops._table', compact('crops', 'view'))->render();
        }

        return view('erp.crops.index', compact('crops', 'view'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:crops,name',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Crop::create($validated);

        return redirect()->back()->with('success', 'Crop created successfully');
    }

    public function update(Request $request, Crop $crop)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:crops,name,' . $crop->id,
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $crop->update($validated);

        return redirect()->back()->with('success', 'Crop updated successfully');
    }

    public function destroy(Crop $crop)
    {
        $crop->delete();
        return redirect()->back()->with('success', 'Crop moved to trash');
    }

    public function restore($id)
    {
        $crop = Crop::onlyTrashed()->findOrFail($id);
        $crop->restore();
        return redirect()->back()->with('success', 'Crop restored successfully');
    }

    public function forceDelete($id)
    {
        $crop = Crop::onlyTrashed()->findOrFail($id);
        $crop->forceDelete();
        return redirect()->back()->with('success', 'Crop permanently deleted');
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
                Crop::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected crops moved to trash';
                break;
            case 'restore':
                Crop::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected crops restored';
                break;
            case 'force-delete':
                Crop::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected crops permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
