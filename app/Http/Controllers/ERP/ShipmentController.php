<?php
namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShipmentTrackingEvent;
use App\Services\OrderService;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(
        private ShippingService $shippingService,
        private OrderService    $orderService,
    ) {}

    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $query  = Shipment::with(['order.party', 'latestEvent'])->latest();

        if ($status) $query->where('status', $status);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhere('shipment_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function($qo) use ($search) {
                      $qo->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('party', function($qp) use ($search) {
                            $qp->where('name', 'like', "%{$search}%");
                        });
                  });
            });
        }

        $shipments = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('erp.shipments._table', compact('shipments'))->render();
        }

        return view('erp.shipments.index', compact('shipments', 'status'));
    }

    public function show(Shipment $shipment)
    {
        $shipment->load(['order.party', 'order.items.product', 'trackingEvents']);
        return view('erp.shipments.show', compact('shipment'));
    }

    /** Create shipment for a packed order */
    public function create(Order $order)
    {
        return view('erp.shipments.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        $validated = $request->validate([
            'carrier'           => 'required|string|max:100',
            'tracking_number'   => 'nullable|string|max:100',
            'tracking_url'      => 'nullable|url|max:500',
            'estimated_delivery'=> 'nullable|date',
            'shipping_cost'     => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string',
        ]);

        try {
            $shipment = $this->shippingService->createShipment($order, $validated);
            $this->orderService->ship($order);
            return redirect()->route('erp.shipments.show', $shipment)->with('success', 'Shipment created and order shipped.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /** Add a tracking event */
    public function addEvent(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'status'      => 'required|string|max:100',
            'location'    => 'nullable|string|max:200',
            'description' => 'nullable|string',
        ]);

        $this->shippingService->addTrackingEvent($shipment, $validated['status'], $validated['location'] ?? null, $validated['description'] ?? null);
        return redirect()->route('erp.shipments.show', $shipment)->with('success', 'Tracking event added.');
    }

    /** Mark delivered */
    public function markDelivered(Request $request, Shipment $shipment)
    {
        $this->shippingService->markDelivered($shipment);
        try {
            $this->orderService->deliver($shipment->order);
        } catch (\Exception $e) {}
        return redirect()->route('erp.shipments.show', $shipment)->with('success', 'Shipment marked as delivered.');
    }

    /** Mark as returned */
    public function markReturned(Request $request, Shipment $shipment)
    {
        $reason = $request->input('reason');
        $this->shippingService->markReturned($shipment, $reason);
        try {
            $this->orderService->markReturned($shipment->order);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->route('erp.shipments.show', $shipment)->with('success', 'Shipment marked as returned and inventory restocked.');
    }
}
