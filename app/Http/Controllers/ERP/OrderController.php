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
        $status = $request->input('status');
        $search = $request->input('search');
        $warehouseId = $request->input('warehouse_id');
        $view = $request->input('view', 'active');
        
        $query = Order::query()
            ->where('type', $type)
            ->with(['party', 'warehouse']);

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                  ->orWhereHas('party', function($pq) use ($search) {
                      $pq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('warehouse', function($wq) use ($search) {
                      $wq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $orders = $query->latest()->paginate(10)->withQueryString();
        $warehouses = Warehouse::all();
        
        if ($request->ajax()) {
            return view('erp.orders._table', compact('orders', 'type', 'view'))->render();
        }

        return view('erp.orders.index', compact('orders', 'type', 'status', 'view', 'warehouses'));
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
        
        if (request()->ajax()) {
            return view('erp.orders._show_modal_content', compact('order'))->render();
        }

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

    public function restore($id)
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->restore();
        return redirect()->back()->with('success', 'Order restored successfully');
    }

    public function forceDelete($id)
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->forceDelete();
        return redirect()->back()->with('success', 'Order permanently deleted');
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No orders selected');
        }

        switch ($action) {
            case 'delete':
                Order::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected orders moved to trash';
                break;
            case 'restore':
                Order::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected orders restored';
                break;
            case 'force-delete':
                Order::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected orders permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
