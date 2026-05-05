<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\LedgerEntry;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $view = $request->input('view', 'active');
        $dateRange = $request->input('date_range');

        $query = Invoice::query()->with(['party', 'order'])->withSum('payments', 'amount');
        $this->applyFilters($request, $query, $view);

        $invoices = $query->latest()->paginate(10)->withQueryString();

        // Dashboard Stats
        $statsQuery = Invoice::query();
        $this->applyFilters($request, $statsQuery, $view);

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'revenue' => (clone $statsQuery)->sum('total_amount'),
            'unpaid_count' => (clone $statsQuery)->where('status', 'unpaid')->count(),
            'unpaid_amount' => (clone $statsQuery)->where('status', 'unpaid')->sum('total_amount'),
        ];

        if ($request->ajax()) {
            return view('erp.invoices._table', compact('invoices', 'view'))->render();
        }

        return view('erp.invoices.index', compact('invoices', 'status', 'view', 'stats', 'dateRange'));
    }

    public function create(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order::with(['party', 'warehouse', 'items.product'])->findOrFail($orderId);
        return view('erp.invoices.create', compact('order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
        ]);

        if (Invoice::where('order_id', $validated['order_id'])->exists()) {
            return redirect()->back()->with('error', 'Invoice has already been generated for this order.');
        }

        DB::transaction(function() use ($validated) {
            $order = Order::with(['items', 'party'])->find($validated['order_id']);
            
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'party_id' => $order->party_id,
                'invoice_number' => 'INV-' . time(),
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'sub_total' => $order->sub_total,
                'tax_amount' => 0,
                'total_amount' => $order->total_amount,
                'status' => 'unpaid',
            ]);

            foreach ($order->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ]);
            }
            
            LedgerEntry::create([
                'party_id' => $order->party_id,
                'entry_date' => $validated['invoice_date'],
                'description' => 'Invoice #' . $invoice->invoice_number . ' for Order #' . $order->order_number,
                'type' => $order->type == 'sale' ? 'debit' : 'credit',
                'amount' => $order->total_amount,
                'reference_type' => 'Invoice',
                'reference_id' => $invoice->id,
            ]);

            $order->update(['status' => 'completed']);
        });

        return redirect()->route('erp.invoices.index')->with('success', 'Invoice generated successfully');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['party', 'order', 'items.product']);
        return view('erp.invoices.show', compact('invoice'));
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Please select at least one invoice');
        }

        switch ($action) {
            case 'delete':
                Invoice::whereIn('id', $ids)->delete();
                $msg = 'Selected invoices moved to trash';
                break;
            case 'change-status':
                $newStatus = $request->input('status');
                if (!$newStatus) return redirect()->back()->with('error', 'Please select a status');
                Invoice::whereIn('id', $ids)->update(['status' => $newStatus]);
                $msg = 'Status updated for selected invoices';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }

    public function export(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = Invoice::query()->with(['party', 'order'])->withSum('payments', 'amount');
        $this->applyFilters($request, $query, $view);
        
        $invoices = $query->latest()->get();
        
        $filename = "invoices_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Invoice Number', 'Date', 'Due Date', 'Party', 'Order Number', 'Subtotal', 'Tax', 'Total', 'Paid', 'Pending', 'Status'];

        $callback = function() use($invoices, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_number,
                    $invoice->invoice_date->format('Y-m-d'),
                    $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-',
                    $invoice->party->name,
                    $invoice->order->order_number ?? '-',
                    $invoice->sub_total,
                    $invoice->tax_amount,
                    $invoice->total_amount,
                    $invoice->payments_sum_amount ?? 0,
                    $invoice->total_amount - ($invoice->payments_sum_amount ?? 0),
                    $invoice->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importPaymentsPreview(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt|max:2048']);
        $path = $request->file('file')->getRealPath();
        $data = array_map('str_getcsv', file($path));
        
        $header = array_shift($data);
        $previewData = [];
        
        foreach ($data as $row) {
            if (count($row) < 5) continue;
            
            $invoiceNum = $row[0];
            $amount = (float)$row[1];
            $date = $row[2];
            $method = strtolower($row[3]);
            $ref = $row[4];
            
            $invoice = Invoice::where('invoice_number', $invoiceNum)->first();
            $status = 'valid';
            $message = 'Ready to process';
            
            if (!$invoice) {
                $status = 'error';
                $message = 'Invoice not found';
            } elseif ($invoice->status === 'paid') {
                $status = 'warning';
                $message = 'Invoice already paid';
            }
            
            $previewData[] = [
                'invoice_number' => $invoiceNum,
                'party' => $invoice->party->name ?? 'N/A',
                'amount' => $amount,
                'date' => $date,
                'method' => $method,
                'reference' => $ref,
                'status' => $status,
                'message' => $message,
                'invoice_id' => $invoice->id ?? null
            ];
        }
        
        return response()->json(['data' => $previewData]);
    }

    public function importPaymentsProcess(Request $request)
    {
        $payments = $request->input('payments', []);
        $count = 0;
        
        DB::transaction(function() use ($payments, &$count) {
            foreach ($payments as $item) {
                $invoice = Invoice::with('order')->find($item['invoice_id']);
                if (!$invoice || $invoice->status === 'paid') continue;
                
                $payment = Payment::create([
                    'invoice_id' => $invoice->id,
                    'party_id' => $invoice->party_id,
                    'payment_number' => 'PAY-' . time() . rand(100, 999),
                    'payment_date' => $item['date'],
                    'amount' => $item['amount'],
                    'payment_method' => $item['method'],
                    'reference_number' => $item['reference'],
                ]);
                
                $type = ($invoice->order && $invoice->order->type == 'sale') ? 'credit' : 'debit';
                
                LedgerEntry::create([
                    'party_id' => $invoice->party_id,
                    'entry_date' => $item['date'],
                    'description' => 'Bulk Import Payment against Invoice #' . $invoice->invoice_number,
                    'type' => $type,
                    'amount' => $item['amount'],
                    'reference_type' => 'Payment',
                    'reference_id' => $payment->id,
                ]);

                // Update invoice status
                $totalPaid = $invoice->payments()->sum('amount');
                if ($totalPaid >= $invoice->total_amount) {
                    $invoice->update(['status' => 'paid']);
                } else {
                    $invoice->update(['status' => 'partial']);
                }
                $count++;
            }
        });
        
        return response()->json(['success' => true, 'message' => "$count payments processed successfully"]);
    }

    public function downloadPaymentsTemplate()
    {
        $filename = "bulk_payments_template.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['invoice_number', 'amount', 'date', 'method', 'reference'];
        $sample = ['INV-1714900000', '1500.00', date('Y-m-d'), 'bank', 'REF-SAMPLE-123'];

        $callback = function() use($columns, $sample) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $sample);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['party', 'order', 'items.product']);
        $pdf = Pdf::loadView('erp.invoices.print', compact('invoice'));
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    public function bulkPrint(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        $ids = array_filter((array)$ids, fn($id) => is_numeric($id) && $id > 0);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No valid orders selected');
        }

        $invoices = Invoice::with(['party', 'order', 'items.product'])
            ->whereIn('order_id', $ids)
            ->latest('id')
            ->get()
            ->unique('order_id');

        if ($invoices->isEmpty()) {
            return redirect()->back()->with('error', 'No invoices found for the selected orders');
        }
        
        $pdf = Pdf::loadView('erp.invoices.bulk-print', compact('invoices'));
        return $pdf->download("invoices-bulk-" . date('Y-m-d') . ".pdf");
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
                $q->where('invoice_number', 'like', '%' . $search . '%')
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
                case 'today': $query->whereDate('invoice_date', now()->today()); break;
                case 'yesterday': $query->whereDate('invoice_date', now()->yesterday()); break;
                case 'this_week': $query->whereBetween('invoice_date', [now()->startOfWeek(), now()->endOfWeek()]); break;
                case 'this_month': $query->whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year); break;
                case 'this_year': $query->whereYear('invoice_date', now()->year); break;
            }
        }
    }
}
