<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = TaxRate::query();

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $taxRates = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();

        if ($request->ajax()) {
            return view('erp.tax-rates._table', compact('taxRates', 'view'))->render();
        }

        return view('erp.tax-rates.index', compact('taxRates', 'view'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cgst' => 'required|numeric|min:0|max:100',
            'sgst' => 'required|numeric|min:0|max:100',
            'igst' => 'required|numeric|min:0|max:100',
        ]);

        TaxRate::create($validated);

        return redirect()->back()->with('success', 'Tax rate created successfully');
    }

    public function update(Request $request, TaxRate $taxRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cgst' => 'required|numeric|min:0|max:100',
            'sgst' => 'required|numeric|min:0|max:100',
            'igst' => 'required|numeric|min:0|max:100',
        ]);

        $taxRate->update($validated);

        return redirect()->back()->with('success', 'Tax rate updated successfully');
    }

    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();
        return redirect()->back()->with('success', 'Tax rate moved to trash');
    }

    public function restore($id)
    {
        $taxRate = TaxRate::onlyTrashed()->findOrFail($id);
        $taxRate->restore();
        return redirect()->back()->with('success', 'Tax rate restored successfully');
    }

    public function forceDelete($id)
    {
        $taxRate = TaxRate::onlyTrashed()->findOrFail($id);
        $taxRate->forceDelete();
        return redirect()->back()->with('success', 'Tax rate permanently deleted');
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
                TaxRate::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected tax rates moved to trash';
                break;
            case 'restore':
                TaxRate::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected tax rates restored';
                break;
            case 'force-delete':
                TaxRate::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected tax rates permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
