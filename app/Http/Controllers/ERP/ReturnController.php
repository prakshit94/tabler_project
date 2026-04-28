<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\ReturnItem;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $returns = OrderReturn::with(['party', 'order'])->latest()->paginate(10);
        return view('erp.returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order::with(['party', 'items.product'])->findOrFail($orderId);
        return view('erp.returns.create', compact('order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'return_date' => 'required|date',
            'reason' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function() use ($validated) {
            $order = Order::find($validated['order_id']);
            
            $return = OrderReturn::create([
                'order_id' => $order->id,
                'party_id' => $order->party_id,
                'return_number' => 'RET-' . time(),
                'return_date' => $validated['return_date'],
                'reason' => $validated['reason'],
                'status' => 'completed',
            ]);

            foreach ($validated['items'] as $itemData) {
                $return->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => 0, // Simplified
                    'total_price' => 0,
                ]);

                // Update stock
                $stock = Stock::firstOrCreate([
                    'product_id' => $itemData['product_id'],
                    'warehouse_id' => $order->warehouse_id,
                ], ['quantity' => 0]);
                
                if ($order->type == 'sale') {
                    $stock->quantity += $itemData['quantity']; // Sale return increases stock
                    $moveType = 'in';
                } else {
                    $stock->quantity -= $itemData['quantity']; // Purchase return decreases stock
                    $moveType = 'out';
                }
                $stock->save();

                StockMovement::create([
                    'product_id' => $itemData['product_id'],
                    'warehouse_id' => $order->warehouse_id,
                    'type' => $moveType,
                    'quantity' => $itemData['quantity'],
                    'reference_type' => 'Return',
                    'reference_id' => $return->id,
                ]);
            }
        });

        return redirect()->route('erp.returns.index')->with('success', 'Return processed successfully');
    }

    public function show(OrderReturn $return)
    {
        $return->load(['party', 'order', 'items.product']);
        return view('erp.returns.show', compact('return'));
    }
}
