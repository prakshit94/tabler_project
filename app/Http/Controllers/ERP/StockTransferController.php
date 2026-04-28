<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $transfers = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'product'])->latest()->paginate(10);
        return view('erp.stock-transfers.index', compact('transfers'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $warehouses = Warehouse::all();
        return view('erp.stock-transfers.create', compact('products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function() use ($validated) {
            // Check stock in from_warehouse
            $fromStock = Stock::where('product_id', $validated['product_id'])
                             ->where('warehouse_id', $validated['from_warehouse_id'])
                             ->first();

            if (!$fromStock || $fromStock->quantity < $validated['quantity']) {
                throw new \Exception('Insufficient stock in source warehouse');
            }

            // Record Transfer
            $transfer = StockTransfer::create([
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id' => $validated['to_warehouse_id'],
                'transfer_date' => $validated['transfer_date'],
                'status' => 'completed',
                'notes' => $validated['notes'],
            ]);

            $transfer->items()->create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
            ]);

            // Update Stocks
            $fromStock->decrement('quantity', $validated['quantity']);
            
            $toStock = Stock::firstOrCreate([
                'product_id' => $validated['product_id'],
                'warehouse_id' => $validated['to_warehouse_id'],
            ], ['quantity' => 0]);
            $toStock->increment('quantity', $validated['quantity']);

            // Record Movements
            StockMovement::create([
                'product_id' => $validated['product_id'],
                'warehouse_id' => $validated['from_warehouse_id'],
                'type' => 'out',
                'quantity' => $validated['quantity'],
                'reference_type' => 'Transfer Out',
            ]);

            StockMovement::create([
                'product_id' => $validated['product_id'],
                'warehouse_id' => $validated['to_warehouse_id'],
                'type' => 'in',
                'quantity' => $validated['quantity'],
                'reference_type' => 'Transfer In',
            ]);
        });

        return redirect()->route('erp.stock-transfers.index')->with('success', 'Stock transferred successfully');
    }
}
