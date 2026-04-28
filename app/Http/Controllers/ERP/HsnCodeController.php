<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\HsnCode;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class HsnCodeController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = HsnCode::query()->with('taxRate');

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $hsnCodes = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();
        $taxRates = TaxRate::all();

        if ($request->ajax()) {
            return view('erp.hsn-codes._table', compact('hsnCodes', 'view'))->render();
        }

        return view('erp.hsn-codes.index', compact('hsnCodes', 'taxRates', 'view'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:hsn_codes,code',
            'description' => 'nullable|string|max:500',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
        ]);

        HsnCode::create($validated);

        return redirect()->back()->with('success', 'HSN code created successfully');
    }

    public function update(Request $request, HsnCode $hsnCode)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:hsn_codes,code,' . $hsnCode->id,
            'description' => 'nullable|string|max:500',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
        ]);

        $hsnCode->update($validated);

        return redirect()->back()->with('success', 'HSN code updated successfully');
    }

    public function destroy(HsnCode $hsnCode)
    {
        $hsnCode->delete();
        return redirect()->back()->with('success', 'HSN code moved to trash');
    }

    public function restore($id)
    {
        $hsnCode = HsnCode::onlyTrashed()->findOrFail($id);
        $hsnCode->restore();
        return redirect()->back()->with('success', 'HSN code restored successfully');
    }

    public function forceDelete($id)
    {
        $hsnCode = HsnCode::onlyTrashed()->findOrFail($id);
        $hsnCode->forceDelete();
        return redirect()->back()->with('success', 'HSN code permanently deleted');
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
                HsnCode::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected HSN codes moved to trash';
                break;
            case 'restore':
                HsnCode::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected HSN codes restored';
                break;
            case 'force-delete':
                HsnCode::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected HSN codes permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
