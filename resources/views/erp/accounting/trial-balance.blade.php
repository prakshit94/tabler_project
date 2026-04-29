@extends('layouts.tabler')
@section('title', 'Trial Balance')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('erp.accounting.index') }}">Accounting</a></li>
                    <li class="breadcrumb-item active">Trial Balance</li>
                </ol>
                <h2 class="page-title">Trial Balance</h2>
            </div>
            <div class="col-auto">
                <button onclick="window.print()" class="btn btn-outline-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Print
                </button>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        @php $balanced = abs($grandDebit - $grandCredit) < 0.01; @endphp
        @if($balanced)
        <div class="alert alert-success d-flex align-items-center mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            Books are balanced — Total Debit = Total Credit = ₹{{ number_format($grandDebit, 2) }}
        </div>
        @else
        <div class="alert alert-danger mb-3">
            ⚠ Unbalanced — Debit: ₹{{ number_format($grandDebit, 2) }} / Credit: ₹{{ number_format($grandCredit, 2) }} / Diff: ₹{{ number_format(abs($grandDebit - $grandCredit), 2) }}
        </div>
        @endif

        <div class="card">
            <div class="card-header"><h3 class="card-title">All Ledger Balances</h3></div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Ledger Name</th>
                            <th>Type</th>
                            <th class="text-end">Total Debit (₹)</th>
                            <th class="text-end">Total Credit (₹)</th>
                            <th class="text-end">Balance (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ledgers as $l)
                        @if($l->total_debit > 0 || $l->total_credit > 0)
                        <tr>
                            <td class="fw-medium">{{ $l->name }}</td>
                            <td><span class="badge bg-{{ $l->type === 'asset'?'blue':($l->type==='liability'?'red':($l->type==='income'?'green':'orange')) }}-lt text-{{ $l->type === 'asset'?'blue':($l->type==='liability'?'red':($l->type==='income'?'green':'orange')) }}">{{ ucfirst($l->type) }}</span></td>
                            <td class="text-end text-green">{{ number_format($l->total_debit, 2) }}</td>
                            <td class="text-end text-red">{{ number_format($l->total_credit, 2) }}</td>
                            <td class="text-end fw-bold {{ $l->balance >= 0 ? 'text-green' : 'text-red' }}">{{ number_format(abs($l->balance), 2) }} {{ $l->balance < 0 ? '(Cr)' : '(Dr)' }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                    <tfoot class="fw-bold bg-light">
                        <tr>
                            <td colspan="2">Grand Total</td>
                            <td class="text-end text-green">{{ number_format($grandDebit, 2) }}</td>
                            <td class="text-end text-red">{{ number_format($grandCredit, 2) }}</td>
                            <td class="text-end {{ $balanced ? 'text-green' : 'text-red' }}">{{ $balanced ? '✓ Balanced' : '✗ Unbalanced' }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
