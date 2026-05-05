<?php
namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PickList;
use App\Models\PickListItem;
use App\Models\Package;
use App\Models\Shipment;
use App\Models\Backorder;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Services\OrderService;
use App\Services\PickingService;
use App\Services\PackingService;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WmsController extends Controller
{
    public function __construct(
        private OrderService  $orderService,
        private PickingService $pickingService,
        private PackingService $packingService,
        private ShippingService $shippingService,
    ) {}

    /** WMS Dashboard */
    public function dashboard()
    {
        $stats = [
            'pending_pick'   => PickList::where('status', 'pending')->count(),
            'in_progress_pick' => PickList::where('status', 'in_progress')->count(),
            'pending_pack'   => Order::where('status', 'picked')->count(),
            'pending_ship'   => Order::where('status', 'packed')->count(),
            'in_transit'     => Shipment::whereNotIn('status', ['delivered', 'returned'])->count(),
            'backorders'     => Backorder::where('status', 'pending')->count(),
            'low_stock'      => Stock::whereRaw(
                'quantity - reserved_qty - committed_qty <= ?',
                [(int) \App\Models\SystemSetting::get('low_stock_threshold', 10)]
            )->count(),
        ];

        $recentPickLists  = PickList::with(['order.party', 'warehouse'])->latest()->take(5)->get();
        $recentShipments  = Shipment::with(['order.party', 'latestEvent'])->latest()->take(5)->get();
        $pendingBackorders = Backorder::with(['product', 'order'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('erp.wms.dashboard', compact('stats', 'recentPickLists', 'recentShipments', 'pendingBackorders'));
    }

    /** ---- PICKING ---- */
    public function pickLists(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $warehouse_id = $request->input('warehouse_id');

        $query = PickList::with(['order.party', 'warehouse', 'assignedTo'])->latest();

        if ($status) $query->where('status', $status);
        if ($warehouse_id) $query->where('warehouse_id', $warehouse_id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pick_list_number', 'like', "%{$search}%")
                  ->orWhereHas('order.party', fn ($qp) => $qp->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('order', fn ($qo) => $qo->where('order_number', 'like', "%{$search}%"));
            });
        }

        $pickLists = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('erp.wms.pick-lists._table', compact('pickLists'))->render();
        }

        $warehouses = Warehouse::all();
        return view('erp.wms.pick-lists.index', compact('pickLists', 'status', 'warehouses'));
    }

    public function generatePickList(Order $order)
    {
        try {
            $pickList = DB::transaction(function () use ($order) {
                if (!in_array($order->status, ['allocated'])) {
                    throw new \RuntimeException("Order must be in 'allocated' status to generate a pick list.");
                }

                $this->orderService->startPicking($order);
                return $this->pickingService->generatePickList($order);
            });

            return redirect()
                ->route('erp.wms.pick-list.show', $pickList)
                ->with('success', "Pick list generated for Order #{$order->order_number}");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function showPickList(PickList $pickList)
    {
        $pickList->load(['order.party', 'warehouse', 'assignedTo', 'items.product', 'items.batch']);
        return view('erp.wms.pick-lists.show', compact('pickList'));
    }

    public function startPickList(PickList $pickList)
    {
        $this->pickingService->startPickList($pickList);
        return redirect()->route('erp.wms.pick-list.show', $pickList)->with('success', 'Picking started.');
    }

    public function recordPick(Request $request, PickListItem $item)
    {
        $validated = $request->validate([
            'picked_qty' => 'required|numeric|min:0'
        ]);

        $this->pickingService->recordPick($item, $validated['picked_qty']);

        $pickList = $item->pickList()->first()?->fresh();

        if ($pickList && $pickList->status === 'completed') {
            try {
                $this->orderService->markPicked($pickList->order);
                return redirect()
                    ->route('erp.wms.packing.show', $pickList->order)
                    ->with('success', 'Picking completed. Starting packing...');
            } catch (\Throwable $e) {}
        }

        return redirect()
            ->route('erp.wms.pick-list.show', $item->pick_list_id)
            ->with('success', 'Pick recorded.');
    }

    /** ---- PACKING ---- */
    public function packingQueue(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status') ?: 'to_pack';
        $warehouse_id = $request->input('warehouse_id');

        $query = Order::with(['party', 'warehouse', 'packages', 'items.product'])->latest();

        if ($status === 'to_pack') {
            $query->whereIn('status', ['picked', 'packing']);
        } elseif ($status !== 'all' && $status) {
            $query->where('status', $status);
        }

        if ($warehouse_id) $query->where('warehouse_id', $warehouse_id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('party', fn ($qp) => $qp->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('erp.wms.packing._table', compact('orders'))->render();
        }

        $warehouses = Warehouse::all();
        return view('erp.wms.packing.index', compact('orders', 'warehouses', 'status'));
    }

    public function showPacking(Order $order)
    {
        $order->load(['items.product', 'packages.items.product']);
        $summary = $this->packingService->getSummary($order);
        return view('erp.wms.packing.show', compact('order', 'summary'));
    }

    public function startPacking(Order $order)
    {
        try {
            $this->orderService->startPacking($order);
            return redirect()->route('erp.wms.packing.show', $order)->with('success', 'Packing started.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function createPackage(Request $request, Order $order)
    {
        $validated = $request->validate([
            'weight'     => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:50',
            'notes'      => 'nullable|string',
        ]);

        $this->packingService->createPackage($order, $validated);

        return redirect()
            ->route('erp.wms.packing.show', $order)
            ->with('success', 'Package created.');
    }

    public function addItemToPackage(Request $request, Package $package)
    {
        $validated = $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'quantity'      => 'required|numeric|min:0.01',
        ]);

        $orderItem = \App\Models\OrderItem::findOrFail($validated['order_item_id']);

        $this->packingService->addItem(
            $package,
            $orderItem->id,
            $orderItem->product_id,
            $validated['quantity']
        );

        return redirect()
            ->route('erp.wms.packing.show', $package->order_id)
            ->with('success', 'Item added to package.');
    }

    public function sealPackage(Package $package)
    {
        $this->packingService->sealPackage($package);

        $order = $package->order()->first()?->fresh();

        if ($order) {
            $summary = $this->packingService->getSummary($order);
            $allSealed = $order->packages()->where('status', '!=', 'packed')->doesntExist();

            if ($summary['is_complete'] && $allSealed && $order->packages()->count() > 0 && in_array($order->status, ['packing', 'picked'])) {
                try {
                    if ($order->status === 'picked') {
                        $this->orderService->startPacking($order);
                    }
                    $this->orderService->markPacked($order);
                    return redirect()
                        ->route('erp.shipments.create', $order->id)
                        ->with('success', 'All packages sealed. Proceeding to shipment...');
                } catch (\Throwable $e) {}
            }
        }

        return redirect()
            ->route('erp.wms.packing.show', $package->order_id)
            ->with('success', 'Package sealed.');
    }
}