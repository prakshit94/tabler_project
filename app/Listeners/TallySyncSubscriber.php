<?php

namespace App\Listeners;

use App\Events\InvoiceCreated;
use App\Events\PaymentReceived;
use App\Services\TallyService;
use Illuminate\Events\Dispatcher;

class TallySyncSubscriber
{
    public function __construct(private TallyService $tallyService) {}

    /**
     * Handle Invoice Created event.
     */
    public function handleInvoiceCreated(InvoiceCreated $event): void
    {
        $invoice = $event->invoice;
        $this->tallyService->queueSync(
            'Invoice',
            $invoice->id,
            'sales_invoice',
            [
                'date' => $invoice->invoice_date->format('Ymd'),
                'narration' => "Sales Invoice #{$invoice->invoice_number}",
                'amount' => $invoice->total_amount,
                'entries' => [
                    ['ledger' => 'Accounts Receivable', 'amount' => $invoice->total_amount, 'is_debit' => true],
                    ['ledger' => 'Sales', 'amount' => $invoice->total_amount - ($invoice->tax_amount ?? 0), 'is_debit' => false],
                    ['ledger' => 'GST Output', 'amount' => $invoice->tax_amount ?? 0, 'is_debit' => false],
                ]
            ]
        );
    }

    /**
     * Handle Payment Received event.
     */
    public function handlePaymentReceived(PaymentReceived $event): void
    {
        $payment = $event->payment;
        $this->tallyService->queueSync(
            'Payment',
            $payment->id,
            'payment_received',
            [
                'date' => ($payment->payment_date ?? now())->format('Ymd'),
                'narration' => "Payment #{$payment->id}",
                'amount' => $payment->amount,
                'entries' => [
                    ['ledger' => $payment->payment_method ?? 'Cash', 'amount' => $payment->amount, 'is_debit' => true],
                    ['ledger' => 'Accounts Receivable', 'amount' => $payment->amount, 'is_debit' => false],
                ]
            ]
        );
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            InvoiceCreated::class,
            [TallySyncSubscriber::class, 'handleInvoiceCreated']
        );

        $events->listen(
            PaymentReceived::class,
            [TallySyncSubscriber::class, 'handlePaymentReceived']
        );
    }
}
