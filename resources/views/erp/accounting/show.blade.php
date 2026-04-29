@extends('layouts.tabler')
@section('title', 'Transaction — ' . $transaction->transaction_number)
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('erp.accounting.index') }}">Accounting</a></li>
                    <li class="breadcrumb-item active">{{ $transaction->transaction_number }}</li>
                </ol>
                <h2 class="page-title">{{ $transaction->transaction_number }}</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Transaction Info</h3></div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-5">Type</dt>
                            <dd class="col-7"><span class="badge bg-blue-lt text-blue">{{ ucfirst(str_replace('_',' ',$transaction->type)) }}</span></dd>
                            <dt class="col-5">Reference</dt>
                            <dd class="col-7">{{ $transaction->reference_type }} #{{ $transaction->reference_id }}</dd>
                            <dt class="col-5">Date</dt>
                            <dd class="col-7">{{ $transaction->transaction_date->format('d M Y') }}</dd>
                            <dt class="col-5">Amount</dt>
                            <dd class="col-7 fw-bold">₹{{ number_format($transaction->total_amount, 2) }}</dd>
                            <dt class="col-5">Narration</dt>
                            <dd class="col-7 text-muted">{{ $transaction->narration ?? '-' }}</dd>
                            <dt class="col-5">Created By</dt>
                            <dd class="col-7">{{ $transaction->createdBy->name ?? 'System' }}</dd>
                            <dt class="col-5">Balanced</dt>
                            <dd class="col-7">
                                @if($transaction->isBalanced())
                                <span class="badge bg-green">✓ Yes</span>
                                @else
                                <span class="badge bg-red">✗ No</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Journal Entries</h3></div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Ledger</th>
                                    <th>Description</th>
                                    <th class="text-end">Debit (₹)</th>
                                    <th class="text-end">Credit (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->entries as $entry)
                                <tr>
                                    <td class="fw-medium">{{ $entry->ledger->name }}</td>
                                    <td class="text-muted">{{ $entry->description }}</td>
                                    <td class="text-end {{ $entry->debit > 0 ? 'text-green fw-bold' : 'text-muted' }}">
                                        {{ $entry->debit > 0 ? '₹'.number_format($entry->debit,2) : '—' }}
                                    </td>
                                    <td class="text-end {{ $entry->credit > 0 ? 'text-red fw-bold' : 'text-muted' }}">
                                        {{ $entry->credit > 0 ? '₹'.number_format($entry->credit,2) : '—' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="fw-bold bg-light">
                                <tr>
                                    <td colspan="2">Total</td>
                                    <td class="text-end text-green">₹{{ number_format($transaction->total_debit, 2) }}</td>
                                    <td class="text-end text-red">₹{{ number_format($transaction->total_credit, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
