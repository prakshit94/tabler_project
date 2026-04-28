@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Record Payment for Invoice #{{ $invoice->invoice_number }}</h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <form action="{{ route('erp.payments.store') }}" method="POST">
        @csrf
        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Payment Date</label>
                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Amount to Pay</label>
                <div class="input-group">
                  <span class="input-group-text">₹</span>
                  <input type="number" name="amount" class="form-control" step="0.01" value="{{ $pendingAmount }}" max="{{ $pendingAmount }}" required>
                </div>
                <small class="text-secondary">Max pending: ₹ {{ number_format($pendingAmount, 2) }}</small>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Payment Method</label>
                <select name="payment_method" class="form-select" required>
                  <option value="cash">Cash</option>
                  <option value="bank">Bank Transfer</option>
                  <option value="online">Online Payment</option>
                  <option value="cheque">Cheque</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Reference Number (Optional)</label>
                <input type="text" name="reference_number" class="form-control" placeholder="TXN ID, Cheque #, etc.">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="card-footer text-end">
          <a href="{{ route('erp.invoices.show', $invoice->id) }}" class="btn btn-link">Back to Invoice</a>
          <button type="submit" class="btn btn-primary">Save Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
