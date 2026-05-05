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
        $search = $request->input('search');
        $status = $request->input('status');
        $view = $request->input('view', 'active');
        $dateRange = $request->input('date_range');

        $query = OrderReturn::query()->with(['party', 'order']);
        $this->applyFilters($request, $query, $view);

        $returns = $query->latest()->paginate(10)->withQueryString();

        // Dashboard Stats
        $statsQuery = OrderReturn::query();
        $this->applyFilters($request, $statsQuery, $view);

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'sale_returns' => (clone $statsQuery)->whereHas('order', fn($q) => $q->where('type', 'sale'))->count(),
            'purchase_returns' => (clone $statsQuery)->whereHas('order', fn($q) => $q->where('type', 'purchase'))->count(),
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
        ];

        if ($request->ajax()) {
            return view('erp.returns._table', compact('returns', 'view'))->render();
        }

        return view('erp.returns.index', compact('returns', 'status', 'view', 'stats', 'dateRange'));
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
                    'unit_price' => 0,
                    'total_price' => 0,
                ]);

                // Update stock
                $stock = Stock::firstOrCreate([
                    'product_id' => $itemData['product_id'],
                    'warehouse_id' => $order->warehouse_id,
                ], ['quantity' => 0]);
                
                if ($order->type == 'sale') {
                    $stock->quantity += $itemData['quantity'];
                    $moveType = 'in';
                } else {
                    $stock->quantity -= $itemData['quantity'];
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

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Please select at least one return');
        }

        switch ($action) {
            case 'delete':
                OrderReturn::whereIn('id', $ids)->delete();
                $msg = 'Selected returns moved to trash';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }

    public function export(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = OrderReturn::query()->with(['party', 'order']);
        $this->applyFilters($request, $query, $view);
        
        $returns = $query->latest()->get();
        
        $filename = "returns_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Return Number', 'Date', 'Party', 'Order Number', 'Status', 'Reason'];

        $callback = function() use($returns, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($returns as $return) {
                fputcsv($file, [
                    $return->return_number,
                    $return->return_date->format('Y-m-d'),
                    $return->party->name,
                    $return->order->order_number ?? '-',
                    $return->status,
                    $return->reason
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function applyFilters(Request $request, $query, $view)
    {
        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('return_number', 'like', '%' . $search . '%')
                  ->orWhereHas('party', function($pq) use ($search) {
                      $pq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('order', function($oq) use ($search) {
                      $oq->where('order_number', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($dateRange = $request->input('date_range')) {
            switch ($dateRange) {
                case 'today': $query->whereDate('return_date', now()->today()); break;
                case 'yesterday': $query->whereDate('return_date', now()->yesterday()); break;
                case 'this_week': $query->whereBetween('return_date', [now()->startOfWeek(), now()->endOfWeek()]); break;
                case 'this_month': $query->whereMonth('return_date', now()->month)->whereYear('return_date', now()->year); break;
                case 'this_year': $query->whereYear('return_date', now()->year); break;
            }
        }
    }
}
