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

use App\Services\OrderService;
use App\Services\InventoryService;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private InventoryService $inventoryService
    ) {}

    public function index(Request $request)
    {
        $type = $request->input('type', 'sale');
        $status = $request->input('status');
        $search = $request->input('search');
        $warehouseId = $request->input('warehouse_id');
        $view = $request->input('view', 'active');
        $dateRange = $request->input('date_range');
        
        $query = Order::query()->with(['party', 'warehouse']);
        $this->applyFilters($request, $query, $type, $view);

        $orders = $query->latest()->paginate(10)->withQueryString();
        $warehouses = Warehouse::all();

        $statsQuery = Order::query();
        $this->applyFilters($request, $statsQuery, $type, $view);
        
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'revenue' => (clone $statsQuery)->sum('total_amount'),
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'shipped' => (clone $statsQuery)->whereIn('status', ['shipped', 'delivered', 'completed'])->count(),
        ];
        
        if ($request->ajax()) {
            return view('erp.orders._table', compact('orders', 'type', 'view'))->render();
        }

        return view('erp.orders.index', compact('orders', 'type', 'status', 'view', 'warehouses', 'stats', 'dateRange'));
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

        $order = DB::transaction(function() use ($validated) {
            $subTotal = 0;
            foreach ($validated['items'] as $item) {
                $subTotal += $item['quantity'] * $item['unit_price'];
            }

            $prefix = $validated['type'] == 'sale' ? 'SO-' : 'PO-';
            $count = Order::where('type', $validated['type'])->count() + 1;
            $orderNumber = $prefix . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'type' => $validated['type'],
                'party_id' => $validated['party_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'order_number' => $orderNumber,
                'order_date' => $validated['order_date'],
                'sub_total' => $subTotal,
                'total_amount' => $subTotal,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $itemData) {
                $order->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemData['quantity'] * $itemData['unit_price'],
                ]);
            }
            return $order;
        });

        return redirect()->route('erp.orders.show', $order)->with('success', 'Order created successfully');
    }

    public function edit(Order $order)
    {
        if (!$order->isEditable()) {
            return redirect()->back()->with('error', 'This order cannot be edited in its current status.');
        }

        $type = $order->type;
        $parties = Party::where('type', $type == 'sale' ? 'customer' : 'vendor')->get();
        $products = Product::where('is_active', true)->get();
        $warehouses = Warehouse::all();
        $order->load('items.product');

        return view('erp.orders.edit', compact('order', 'type', 'parties', 'products', 'warehouses'));
    }

    public function update(Request $request, Order $order)
    {
        if (!$order->isEditable()) {
            return redirect()->back()->with('error', 'This order cannot be edited in its current status.');
        }

        $validated = $request->validate([
            'party_id' => 'required|exists:parties,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($validated, $order) {
            $subTotal = 0;
            foreach ($validated['items'] as $item) {
                $subTotal += $item['quantity'] * $item['unit_price'];
            }

            $order->update([
                'party_id' => $validated['party_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'order_date' => $validated['order_date'],
                'sub_total' => $subTotal,
                'total_amount' => $subTotal,
            ]);

            $order->items()->delete();
            foreach ($validated['items'] as $itemData) {
                $order->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemData['quantity'] * $itemData['unit_price'],
                ]);
            }
        });

        return redirect()->route('erp.orders.show', $order)->with('success', 'Order updated successfully');
    }

    public function destroy(Order $order)
    {
        $type = $order->type;
        $order->delete();
        return redirect()->route('erp.orders.index', ['type' => $type])->with('success', 'Order moved to trash');
    }

    public function show(Order $order)
    {
        $order->load([
            'party', 'warehouse', 'items.product',
            'allocations.batch', 
            'pickLists', 'shipments', 'backorders'
        ]);
        
        if (request()->ajax()) {
            return view('erp.orders._show_modal_content', compact('order'))->render();
        }

        return view('erp.orders.show', compact('order'));
    }
    
    public function receive(Order $order)
    {
        try {
            $this->orderService->receive($order);
            return redirect()->back()->with('success', 'Items received and stock updated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $status = $request->status;
        if ($status == 'completed') {
            if ($order->type == 'purchase') {
                return $this->receive($order);
            } else {
                // For sale, we usually go through WMS, but if legacy:
                $order->update(['status' => 'completed']);
            }
        } else {
            $order->update(['status' => $status]);
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
            case 'change-status':
                $newStatus = $request->input('status');
                if (!$newStatus) return redirect()->back()->with('error', 'Please select a status');
                
                $orders = Order::whereIn('id', $ids)->get();
                $successCount = 0;
                $errors = [];

                foreach ($orders as $order) {
                    try {
                        switch ($newStatus) {
                            case 'confirmed':
                                $this->orderService->confirm($order);
                                break;
                            case 'allocated':
                                $this->orderService->allocate($order);
                                break;
                            case 'delivered':
                                $this->orderService->deliver($order);
                                break;
                            case 'closed':
                                $this->orderService->close($order);
                                break;
                            case 'cancelled':
                                $this->orderService->cancel($order);
                                break;
                            case 'on_hold':
                                $this->orderService->hold($order);
                                break;
                            default:
                                // Fallback for statuses without specific service methods
                                $order->update(['status' => $newStatus]);
                                break;
                        }
                        $successCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Order #{$order->order_number}: " . $e->getMessage();
                    }
                }

                if (!empty($errors)) {
                    $errorMsg = "Updated {$successCount} orders. Errors: " . implode(', ', $errors);
                    return redirect()->back()->with('warning', $errorMsg);
                }
                
                $msg = "Successfully updated status for {$successCount} orders";
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }

    public function export(Request $request)
    {
        $type = $request->input('type', 'sale');
        $view = $request->input('view', 'active');
        
        $query = Order::query()->with(['party', 'warehouse']);
        $this->applyFilters($request, $query, $type, $view);
        
        $orders = $query->latest()->get();
        
        $filename = "orders_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Order Number', 'Date', 'Type', 'Party', 'Warehouse', 'Subtotal', 'Tax', 'Discount', 'Total', 'Status', 'Payment Method'];

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->order_date->format('Y-m-d'),
                    ucfirst($order->type),
                    $order->party->name,
                    $order->warehouse->name,
                    $order->sub_total,
                    $order->tax_amount,
                    $order->discount,
                    $order->total_amount,
                    $order->status,
                    $order->payment_method
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function applyFilters(Request $request, $query, $type, $view)
    {
        $query->where('type', $type);
        if ($view === 'trash') $query->onlyTrashed();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($warehouseId = $request->input('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($search = $request->input('search')) {
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

        if ($dateRange = $request->input('date_range')) {
            switch ($dateRange) {
                case 'today': $query->whereDate('order_date', now()->today()); break;
                case 'yesterday': $query->whereDate('order_date', now()->yesterday()); break;
                case 'this_week': $query->whereBetween('order_date', [now()->startOfWeek(), now()->endOfWeek()]); break;
                case 'this_month': $query->whereMonth('order_date', now()->month)->whereYear('order_date', now()->year); break;
                case 'this_year': $query->whereYear('order_date', now()->year); break;
            }
        }
    }
}
