@extends('layouts.tabler')
@section('title', 'Ledgers')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('erp.accounting.index') }}">Accounting</a></li>
                    <li class="breadcrumb-item active">Ledgers</li>
                </ol>
                <h2 class="page-title">Chart of Accounts</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr><th>Ledger Name</th><th>Type</th><th>Opening Balance</th><th>Entries</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($ledgers as $ledger)
                        <tr>
                            <td class="fw-medium">{{ $ledger->name }}</td>
                            <td>
                                @php
                                    $cls = match($ledger->type ?? '') {
                                        'asset'    => 'bg-blue-lt text-blue',
                                        'liability'=> 'bg-red-lt text-red',
                                        'income'   => 'bg-green-lt text-green',
                                        'expense'  => 'bg-orange-lt text-orange',
                                        default    => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $cls }}">{{ ucfirst($ledger->type ?? '-') }}</span>
                            </td>
                            <td>₹{{ number_format($ledger->opening_balance ?? 0, 2) }}</td>
                            <td>{{ $ledger->entries_count }}</td>
                            <td>
                                <a href="{{ route('erp.accounting.ledger-statement', $ledger) }}" class="btn btn-sm btn-outline-primary">Statement</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No ledgers yet. They are created automatically when transactions are recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $ledgers->links() }}</div>
        </div>
    </div>
</div>
@endsection
