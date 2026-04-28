<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::query()->with(['product', 'warehouse']);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $stocks = $query->paginate($request->input('per_page', 10))->withQueryString();
        $warehouses = Warehouse::all();

        return view('erp.inventory.index', compact('stocks', 'warehouses'));
    }

    public function movements(Request $request)
    {
        $query = StockMovement::query()->with(['product', 'warehouse', 'batch']);

        if ($request->filled('search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $movements = $query->latest()->paginate(20);
        return view('erp.inventory.movements', compact('movements'));
    }

    public function adjustStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric',
            'type' => 'required|in:in,out,adjustment',
            'reason' => 'nullable|string',
        ]);

        DB::transaction(function() use ($validated) {
            $stock = Stock::firstOrCreate([
                'product_id' => $validated['product_id'],
                'warehouse_id' => $validated['warehouse_id'],
            ], ['quantity' => 0]);

            $oldQty = $stock->quantity;
            if ($validated['type'] == 'in') {
                $stock->quantity += $validated['quantity'];
            } elseif ($validated['type'] == 'out') {
                $stock->quantity -= $validated['quantity'];
            } else {
                $stock->quantity = $validated['quantity'];
            }
            $stock->save();

            StockMovement::create([
                'product_id' => $validated['product_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'reference_type' => 'Manual Adjustment',
                'description' => $validated['reason'],
            ]);
        });

        return redirect()->back()->with('success', 'Stock adjusted successfully');
    }
}
