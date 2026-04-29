<?php
namespace App\Services;

use App\Models\AccountingTransaction;
use App\Models\AccountingEntry;
use App\Models\Ledger;
use App\Models\Invoice;
use App\Models\Payment;
use App\Events\InvoiceCreated;
use App\Events\PaymentReceived;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AccountingService
{
    /**
     * Create double-entry accounting entries for a sales invoice.
     * Dr: Accounts Receivable
     * Cr: Sales + GST Output
     */
    public function recordSalesInvoice(Invoice $invoice): AccountingTransaction
    {
        return DB::transaction(function () use ($invoice) {
            $txn = $this->createTransaction('sales_invoice', 'Invoice', $invoice->id, $invoice->total_amount, $invoice->invoice_date, "Sales Invoice #{$invoice->invoice_number}");

            $arLedger   = $this->findOrCreateLedger('Accounts Receivable', 'asset');
            $salesLedger = $this->findOrCreateLedger('Sales', 'income');
            $gstLedger   = $this->findOrCreateLedger('GST Output', 'liability');

            $taxAmount = $invoice->tax_amount ?? 0;
            $salesAmount = $invoice->total_amount - $taxAmount;

            // Debit: Accounts Receivable (full amount)
            $this->createEntry($txn, $arLedger, $invoice->total_amount, 0, "AR for Invoice #{$invoice->invoice_number}", $invoice->invoice_date);
            // Credit: Sales
            $this->createEntry($txn, $salesLedger, 0, $salesAmount, "Sales Revenue", $invoice->invoice_date);
            // Credit: GST Output (if any)
            if ($taxAmount > 0) {
                $this->createEntry($txn, $gstLedger, 0, $taxAmount, "GST Output Tax", $invoice->invoice_date);
            }

            event(new InvoiceCreated($invoice));

            return $txn;
        });
    }

    /**
     * Record payment received.
     * Dr: Cash/Bank
     * Cr: Accounts Receivable
     */
    public function recordPaymentReceived(Payment $payment): AccountingTransaction
    {
        return DB::transaction(function () use ($payment) {
            $txn = $this->createTransaction('payment_received', 'Payment', $payment->id, $payment->amount, $payment->payment_date ?? today(), "Payment #{$payment->id}");

            $bankLedger = $this->findOrCreateLedger($payment->payment_method ?? 'Cash', 'asset');
            $arLedger   = $this->findOrCreateLedger('Accounts Receivable', 'asset');

            $this->createEntry($txn, $bankLedger, $payment->amount, 0, "Payment Received", $payment->payment_date ?? today());
            $this->createEntry($txn, $arLedger, 0, $payment->amount, "AR Settled", $payment->payment_date ?? today());

            event(new PaymentReceived($payment));

            return $txn;
        });
    }

    /**
     * Record purchase.
     * Dr: Inventory
     * Cr: Accounts Payable
     */
    public function recordPurchase(\App\Models\Order $order): AccountingTransaction
    {
        return DB::transaction(function () use ($order) {
            $txn = $this->createTransaction('purchase', 'Order', $order->id, $order->total_amount, $order->order_date, "Purchase Order #{$order->order_number}");

            $invLedger = $this->findOrCreateLedger('Inventory', 'asset');
            $apLedger  = $this->findOrCreateLedger('Accounts Payable', 'liability');

            $this->createEntry($txn, $invLedger, $order->total_amount, 0, "Inventory Purchase", $order->order_date);
            $this->createEntry($txn, $apLedger, 0, $order->total_amount, "AP for PO #{$order->order_number}", $order->order_date);

            return $txn;
        });
    }

    /**
     * Record COGS when order is shipped.
     * Dr: Cost of Goods Sold
     * Cr: Inventory
     */
    public function recordCOGS(\App\Models\Order $order, float $costAmount): AccountingTransaction
    {
        return DB::transaction(function () use ($order, $costAmount) {
            $txn = $this->createTransaction('cogs', 'Order', $order->id, $costAmount, today(), "COGS for Order #{$order->order_number}");

            $cogsLedger = $this->findOrCreateLedger('Cost of Goods Sold', 'expense');
            $invLedger  = $this->findOrCreateLedger('Inventory', 'asset');

            $this->createEntry($txn, $cogsLedger, $costAmount, 0, "COGS", today());
            $this->createEntry($txn, $invLedger, 0, $costAmount, "Inventory Reduction", today());

            return $txn;
        });
    }

    /**
     * Record sales return.
     * Dr: Sales Return
     * Cr: Accounts Receivable
     */
    public function recordSalesReturn(\App\Models\OrderReturn $return, float $amount): AccountingTransaction
    {
        return DB::transaction(function () use ($return, $amount) {
            $txn = $this->createTransaction('sales_return', 'Return', $return->id, $amount, today(), "Sales Return #{$return->return_number}");

            $retLedger = $this->findOrCreateLedger('Sales Returns', 'income');
            $arLedger  = $this->findOrCreateLedger('Accounts Receivable', 'asset');

            $this->createEntry($txn, $retLedger, $amount, 0, "Sales Return", today());
            $this->createEntry($txn, $arLedger, 0, $amount, "AR Credit for Return", today());

            return $txn;
        });
    }

    private function createTransaction(string $type, string $refType, int $refId, float $amount, $date, string $narration): AccountingTransaction
    {
        return AccountingTransaction::create([
            'transaction_number' => 'TXN-' . strtoupper(uniqid()),
            'type'               => $type,
            'reference_type'     => $refType,
            'reference_id'       => $refId,
            'total_amount'       => $amount,
            'transaction_date'   => $date,
            'narration'          => $narration,
            'created_by'         => Auth::id(),
        ]);
    }

    private function createEntry(AccountingTransaction $txn, Ledger $ledger, float $debit, float $credit, string $description, $date): AccountingEntry
    {
        return AccountingEntry::create([
            'transaction_id' => $txn->id,
            'ledger_id'      => $ledger->id,
            'debit'          => $debit,
            'credit'         => $credit,
            'description'    => $description,
            'entry_date'     => $date,
        ]);
    }

    private function findOrCreateLedger(string $name, string $type): Ledger
    {
        return Ledger::firstOrCreate(['name' => $name], ['type' => $type, 'opening_balance' => 0]);
    }
}
