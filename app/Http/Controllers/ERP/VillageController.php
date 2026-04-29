<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Village;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = Village::query();

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('village_name', 'like', "%{$search}%")
                  ->orWhere('taluka_name', 'like', "%{$search}%")
                  ->orWhere('district_name', 'like', "%{$search}%")
                  ->orWhere('post_so_name', 'like', "%{$search}%")
                  ->orWhere('pincode', 'like', "{$search}%");
            });
        }

        $villages = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();

        if ($request->ajax()) {
            return view('erp.villages._table', compact('villages', 'view'))->render();
        }

        return view('erp.villages.index', compact('villages', 'view'));
    }

    public function search(Request $request)
    {
        $q = $request->input('q');
        if (empty($q)) {
            return response()->json([]);
        }

        $villages = Village::where('village_name', 'like', "%{$q}%")
            ->orWhere('taluka_name', 'like', "%{$q}%")
            ->orWhere('district_name', 'like', "%{$q}%")
            ->orWhere('pincode', 'like', "{$q}%")
            ->limit(20)
            ->get();

        return response()->json($villages);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'village_name' => 'required|string|max:255',
            'pincode'      => 'required|string|max:6',
            'post_so_name' => 'nullable|string|max:255',
            'taluka_name'  => 'nullable|string|max:255',
            'district_name'=> 'nullable|string|max:255',
            'state_name'   => 'nullable|string|max:255',
        ]);

        Village::create($validated);

        return redirect()->back()->with('success', 'Village created successfully');
    }

    public function update(Request $request, Village $village)
    {
        $validated = $request->validate([
            'village_name' => 'required|string|max:255',
            'pincode'      => 'required|string|max:6',
            'post_so_name' => 'nullable|string|max:255',
            'taluka_name'  => 'nullable|string|max:255',
            'district_name'=> 'nullable|string|max:255',
            'state_name'   => 'nullable|string|max:255',
        ]);

        $village->update($validated);

        return redirect()->back()->with('success', 'Village updated successfully');
    }

    public function destroy(Village $village)
    {
        $village->delete();
        return redirect()->back()->with('success', 'Village moved to trash');
    }

    public function restore($id)
    {
        $village = Village::onlyTrashed()->findOrFail($id);
        $village->restore();
        return redirect()->back()->with('success', 'Village restored successfully');
    }

    public function forceDelete($id)
    {
        $village = Village::onlyTrashed()->findOrFail($id);
        $village->forceDelete();
        return redirect()->back()->with('success', 'Village permanently deleted');
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
                Village::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected items moved to trash';
                break;
            case 'restore':
                Village::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected items restored';
                break;
            case 'force-delete':
                Village::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected items permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
