@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Invoices</h2>
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
              <th>Invoice #</th>
              <th>Date</th>
              <th>Party</th>
              <th>Order #</th>
              <th>Total Amount</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($invoices as $invoice)
            <tr>
              <td><a href="{{ route('erp.invoices.show', $invoice->id) }}">{{ $invoice->invoice_number }}</a></td>
              <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
              <td>{{ $invoice->party->name }}</td>
              <td>{{ $invoice->order->order_number ?? '-' }}</td>
              <td>₹ {{ number_format($invoice->total_amount, 2) }}</td>
              <td>
                @php
                  $badgeClass = match($invoice->status) {
                    'unpaid' => 'bg-yellow-lt',
                    'paid' => 'bg-green-lt',
                    'partial' => 'bg-blue-lt',
                    default => 'bg-secondary-lt'
                  };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ ucfirst($invoice->status) }}</span>
              </td>
              <td class="text-end">
                <a href="{{ route('erp.invoices.show', $invoice->id) }}" class="btn btn-sm btn-white">View</a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center text-secondary">No invoices found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex align-items-center">
        {{ $invoices->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
