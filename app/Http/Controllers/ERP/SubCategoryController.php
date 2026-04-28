<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = SubCategory::query()->with('category');

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('category', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        $subCategories = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();
        $categories = Category::all();

        if ($request->ajax()) {
            return view('erp.sub-categories._table', compact('subCategories', 'view'))->render();
        }

        return view('erp.sub-categories.index', compact('subCategories', 'categories', 'view'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        SubCategory::create($validated);

        return redirect()->back()->with('success', 'Sub-category created successfully');
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        $subCategory->update($validated);

        return redirect()->back()->with('success', 'Sub-category updated successfully');
    }

    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();
        return redirect()->back()->with('success', 'Sub-category moved to trash');
    }

    public function restore($id)
    {
        $subCategory = SubCategory::onlyTrashed()->findOrFail($id);
        $subCategory->restore();
        return redirect()->back()->with('success', 'Sub-category restored successfully');
    }

    public function forceDelete($id)
    {
        $subCategory = SubCategory::onlyTrashed()->findOrFail($id);
        $subCategory->forceDelete();
        return redirect()->back()->with('success', 'Sub-category permanently deleted');
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
                SubCategory::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected sub-categories moved to trash';
                break;
            case 'restore':
                SubCategory::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected sub-categories restored';
                break;
            case 'force-delete':
                SubCategory::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected sub-categories permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
