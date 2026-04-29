@extends('layouts.tabler')
@section('title', 'Ledger Statement — ' . $ledger->name)
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('erp.accounting.ledgers') }}">Ledgers</a></li>
                    <li class="breadcrumb-item active">{{ $ledger->name }}</li>
                </ol>
                <h2 class="page-title">{{ $ledger->name }} — Statement</h2>
            </div>
            <div class="col-auto">
                <button onclick="window.print()" class="btn btn-outline-secondary">Print</button>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        {{-- Date Filter --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">From</label>
                        <input type="date" name="from" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To</label>
                        <input type="date" name="to" class="form-control" value="{{ $to }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Entries: {{ $from }} to {{ $to }}</h3>
                <div class="card-options">
                    <span class="me-3">Dr: <strong class="text-green">₹{{ number_format($totalDebit, 2) }}</strong></span>
                    <span>Cr: <strong class="text-red">₹{{ number_format($totalCredit, 2) }}</strong></span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr><th>Date</th><th>Txn #</th><th>Description</th><th class="text-end">Debit (₹)</th><th class="text-end">Credit (₹)</th></tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $e)
                        <tr>
                            <td>{{ $e->entry_date->format('d M Y') }}</td>
                            <td><a href="{{ route('erp.accounting.show', $e->transaction_id) }}" class="text-decoration-none">{{ $e->transaction->transaction_number }}</a></td>
                            <td>{{ $e->description ?? '-' }}</td>
                            <td class="text-end {{ $e->debit > 0 ? 'text-green fw-bold' : 'text-muted' }}">{{ $e->debit > 0 ? number_format($e->debit,2) : '—' }}</td>
                            <td class="text-end {{ $e->credit > 0 ? 'text-red fw-bold' : 'text-muted' }}">{{ $e->credit > 0 ? number_format($e->credit,2) : '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No entries in this period</td></tr>
                        @endforelse
                    </tbody>
                    @if($entries->isNotEmpty())
                    <tfoot class="fw-bold bg-light">
                        <tr>
                            <td colspan="3">Total</td>
                            <td class="text-end text-green">{{ number_format($totalDebit, 2) }}</td>
                            <td class="text-end text-red">{{ number_format($totalCredit, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
