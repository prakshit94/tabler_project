<?php
namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Backorder;
use App\Models\Stock;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class BackorderController extends Controller
{
    public function __construct(private InventoryService $inventory) {}

    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');
        $backorders = Backorder::with(['order.party', 'product', 'warehouse'])
            ->where('status', $status)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('erp.backorders.index', compact('backorders', 'status'));
    }

    public function show(Backorder $backorder)
    {
        $backorder->load(['order.items.product', 'product', 'warehouse']);
        $stockLevel = Stock::where('product_id', $backorder->product_id)
            ->where('warehouse_id', $backorder->warehouse_id)->first();
        return view('erp.backorders.show', compact('backorder', 'stockLevel'));
    }

    /** Manually fulfill a backorder when stock arrives */
    public function fulfill(Request $request, Backorder $backorder)
    {
        $validated = $request->validate(['fulfilled_qty' => 'required|numeric|min:0.01']);
        $qty = min($validated['fulfilled_qty'], $backorder->pending_qty - $backorder->fulfilled_qty);

        $backorder->increment('fulfilled_qty', $qty);
        $newStatus = $backorder->fulfilled_qty >= $backorder->pending_qty ? 'fulfilled' : 'waiting_stock';
        $backorder->update([
            'status'       => $newStatus,
            'fulfilled_at' => $newStatus === 'fulfilled' ? now() : null,
        ]);

        return redirect()->route('erp.backorders.index')->with('success', "Backorder {$newStatus}.");
    }

    public function cancel(Backorder $backorder)
    {
        $backorder->update(['status' => 'cancelled']);
        return redirect()->route('erp.backorders.index')->with('success', 'Backorder cancelled.');
    }
}
