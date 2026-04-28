<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::query()->with(['party', 'order']);

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        $invoices = $query->latest()->paginate(10)->withQueryString();
        return view('erp.invoices.index', compact('invoices'));
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
}
