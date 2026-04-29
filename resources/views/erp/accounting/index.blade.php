@extends('layouts.tabler')
@section('title', 'Accounting Journal')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Accounting Journal</h2>
                <p class="page-subtitle">All double-entry accounting transactions</p>
            </div>
            <div class="col-auto gap-2 d-flex">
                <a href="{{ route('erp.accounting.trial-balance') }}" class="btn btn-outline-secondary">Trial Balance</a>
                <a href="{{ route('erp.accounting.ledgers') }}" class="btn btn-outline-secondary">Ledgers</a>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        {{-- Filter --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($types as $t)
                            <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="from" class="form-control" value="{{ $from }}" placeholder="From Date">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="to" class="form-control" value="{{ $to }}" placeholder="To Date">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('erp.accounting.index') }}" class="btn btn-ghost">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr><th>Txn #</th><th>Date</th><th>Type</th><th>Reference</th><th>Amount</th><th>Status</th><th class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                        <tr>
                            <td><a href="{{ route('erp.accounting.show', $txn) }}" class="fw-medium text-decoration-none">{{ $txn->transaction_number }}</a></td>
                            <td>{{ $txn->transaction_date->format('d M Y') }}</td>
                            <td><span class="badge bg-blue-lt text-blue">{{ ucfirst(str_replace('_',' ',$txn->type)) }}</span></td>
                            <td class="text-muted">{{ $txn->reference_type }} #{{ $txn->reference_id }}</td>
                            <td class="fw-bold">₹{{ number_format($txn->total_amount, 2) }}</td>
                            <td>
                                @if($txn->isBalanced())
                                    <span class="badge bg-green-lt text-green">Balanced</span>
                                @else
                                    <span class="badge bg-red-lt text-red">Unbalanced</span>
                                @endif
                            </td>
                            <td class="text-end"><a href="{{ route('erp.accounting.show', $txn) }}" class="btn btn-sm btn-outline-primary">Details</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No transactions found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $transactions->links() }}</div>
        </div>
    </div>
</div>
@endsection
