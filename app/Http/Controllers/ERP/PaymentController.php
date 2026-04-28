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
        $query = Payment::query()->with(['invoice.party']);

        if ($request->filled('search')) {
            $query->where('payment_number', 'like', '%' . $request->search . '%');
        }

        $payments = $query->latest()->paginate(10)->withQueryString();
        return view('erp.payments.index', compact('payments'));
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
            // Sale: Customer pays us -> Credit Customer
            // Purchase: We pay vendor -> Debit Vendor
            $type = $invoice->order->type == 'sale' ? 'credit' : 'debit';
            
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
}
