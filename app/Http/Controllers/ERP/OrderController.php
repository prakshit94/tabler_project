<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Party;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'sale');
        $query = Order::query()->where('type', $type)->with(['party', 'warehouse']);

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate(10)->withQueryString();
        return view('erp.orders.index', compact('orders', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->input('type', 'sale');
        $parties = Party::where('type', $type == 'sale' ? 'customer' : 'vendor')->get();
        $products = Product::where('is_active', true)->get();
        $warehouses = Warehouse::all();
        return view('erp.orders.create', compact('type', 'parties', 'products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:sale,purchase',
            'party_id' => 'required|exists:parties,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($validated) {
            $subTotal = 0;
            foreach ($validated['items'] as $item) {
                $subTotal += $item['quantity'] * $item['unit_price'];
            }

            $order = Order::create([
                'type' => $validated['type'],
                'party_id' => $validated['party_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'order_number' => ($validated['type'] == 'sale' ? 'SO-' : 'PO-') . time(),
                'order_date' => $validated['order_date'],
                'sub_total' => $subTotal,
                'total_amount' => $subTotal, // Simplified
                'status' => 'pending',
            ]);

            foreach ($validated['items'] as $itemData) {
                $order->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemData['quantity'] * $itemData['unit_price'],
                ]);
                
                // Update stock if it's a purchase (optional, usually on delivery, but let's do it here for simplicity if it's "completed")
            }
        });

        return redirect()->route('erp.orders.index', ['type' => $validated['type']])->with('success', 'Order created successfully');
    }

    public function show(Order $order)
    {
        $order->load(['party', 'warehouse', 'items.product']);
        return view('erp.orders.show', compact('order'));
    }
    
    public function updateStatus(Request $request, Order $order)
    {
        $status = $request->status;
        $order->update(['status' => $status]);
        
        if ($status == 'completed') {
            // Process Stock Movements
            foreach ($order->items as $item) {
                $stock = Stock::firstOrCreate([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $order->warehouse_id,
                ], ['quantity' => 0]);
                
                if ($order->type == 'purchase') {
                    $stock->quantity += $item->quantity;
                    $moveType = 'in';
                } else {
                    $stock->quantity -= $item->quantity;
                    $moveType = 'out';
                }
                $stock->save();
                
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $order->warehouse_id,
                    'type' => $moveType,
                    'quantity' => $item->quantity,
                    'reference_type' => 'Order',
                    'reference_id' => $order->id,
                ]);
            }
        }
        
        return redirect()->back()->with('success', 'Order status updated');
    }
}
