@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Payments</h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap">
          <thead>
            <tr>
              <th>Payment #</th>
              <th>Date</th>
              <th>Party</th>
              <th>Invoice #</th>
              <th>Amount</th>
              <th>Method</th>
              <th>Ref #</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payments as $payment)
            <tr>
              <td>{{ $payment->payment_number }}</td>
              <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
              <td>{{ $payment->invoice->party->name ?? 'N/A' }}</td>
              <td>{{ $payment->invoice->invoice_number ?? '-' }}</td>
              <td><strong>₹ {{ number_format($payment->amount, 2) }}</strong></td>
              <td><span class="badge bg-azure-lt">{{ ucfirst($payment->payment_method) }}</span></td>
              <td>{{ $payment->reference_number ?? '-' }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center text-secondary">No payments found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex align-items-center">
        {{ $payments->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
