<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $paymentMethod = $request->input('payment_method');
        $view = $request->input('view', 'active');
        $dateRange = $request->input('date_range');

        $query = Payment::query()->with(['invoice.party', 'party']);
        $this->applyFilters($request, $query, $view);

        $payments = $query->latest()->paginate(10)->withQueryString();

        // Dashboard Stats
        $statsQuery = Payment::query();
        $this->applyFilters($request, $statsQuery, $view);

        $stats = [
            'total_count' => (clone $statsQuery)->count(),
            'total_amount' => (clone $statsQuery)->sum('amount'),
            'cash_amount' => (clone $statsQuery)->where('payment_method', 'cash')->sum('amount'),
            'bank_amount' => (clone $statsQuery)->whereIn('payment_method', ['bank', 'online', 'cheque'])->sum('amount'),
        ];

        if ($request->ajax()) {
            return view('erp.payments._table', compact('payments', 'view'))->render();
        }

        return view('erp.payments.index', compact('payments', 'paymentMethod', 'view', 'stats', 'dateRange'));
    }

    public function create(Request $request)
    {
        $invoiceId = $request->invoice_id;
        $invoice = Invoice::with(['party', 'order'])->findOrFail($invoiceId);
        
        $pendingAmount = $invoice->total_amount - $invoice->payments()->sum('amount');
        return view('erp.payments.create', compact('invoice', 'pendingAmount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,cheque,online',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function() use ($validated) {
            $invoice = Invoice::with('order')->find($validated['invoice_id']);
            
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'party_id' => $invoice->party_id,
                'payment_number' => 'PAY-' . time(),
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
            ]);
            
            // Create Ledger Entry
            $type = ($invoice->order && $invoice->order->type == 'sale') ? 'credit' : 'debit';
            
            LedgerEntry::create([
                'party_id' => $invoice->party_id,
                'entry_date' => $validated['payment_date'],
                'description' => 'Payment against Invoice #' . $invoice->invoice_number,
                'type' => $type,
                'amount' => $validated['amount'],
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
        });

        return redirect()->route('erp.payments.index')->with('success', 'Payment recorded successfully');
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Please select at least one payment');
        }

        switch ($action) {
            case 'delete':
                Payment::whereIn('id', $ids)->delete();
                $msg = 'Selected payments moved to trash';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }

    public function export(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = Payment::query()->with(['invoice.party', 'party']);
        $this->applyFilters($request, $query, $view);
        
        $payments = $query->latest()->get();
        
        $filename = "payments_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Payment Number', 'Date', 'Party', 'Invoice Number', 'Amount', 'Method', 'Reference #', 'Notes'];

        $callback = function() use($payments, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->payment_number,
                    $payment->payment_date->format('Y-m-d'),
                    $payment->party->name ?? $payment->invoice->party->name ?? '-',
                    $payment->invoice->invoice_number ?? '-',
                    $payment->amount,
                    ucfirst($payment->payment_method),
                    $payment->reference_number ?? '-',
                    $payment->notes
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

        if ($method = $request->input('payment_method')) {
            $query->where('payment_method', $method);
        }

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('payment_number', 'like', '%' . $search . '%')
                  ->orWhereHas('party', function($pq) use ($search) {
                      $pq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('invoice', function($iq) use ($search) {
                      $iq->where('invoice_number', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($dateRange = $request->input('date_range')) {
            switch ($dateRange) {
                case 'today': $query->whereDate('payment_date', now()->today()); break;
                case 'yesterday': $query->whereDate('payment_date', now()->yesterday()); break;
                case 'this_week': $query->whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()]); break;
                case 'this_month': $query->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year); break;
                case 'this_year': $query->whereYear('payment_date', now()->year); break;
            }
        }
    }
}
