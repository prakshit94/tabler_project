@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">General Ledger</h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="row row-cards">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <form action="{{ route('erp.ledgers.index') }}" method="GET" class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Select Party (Customer/Vendor)</label>
                <select name="party_id" class="form-select" onchange="this.form.submit()">
                  <option value="">All Parties</option>
                  @foreach($parties as $p)
                  <option value="{{ $p->id }}" @selected($partyId == $p->id)>{{ $p->name }} ({{ ucfirst($p->type) }})</option>
                  @endforeach
                </select>
              </div>
              @if($partyId)
              <div class="col-md-6 text-end">
                <div class="h3 mb-0">Current Balance: <span class="{{ $balance >= 0 ? 'text-green' : 'text-red' }}">₹ {{ number_format(abs($balance), 2) }} {{ $balance >= 0 ? 'Cr' : 'Dr' }}</span></div>
              </div>
              @endif
            </form>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card">
          <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Description</th>
                  <th>Reference</th>
                  <th class="text-end">Debit (Dr)</th>
                  <th class="text-end">Credit (Cr)</th>
                </tr>
              </thead>
              <tbody>
                @forelse($entries as $entry)
                <tr>
                  <td>{{ $entry->entry_date->format('d M Y') }}</td>
                  <td>{{ $entry->description }}</td>
                  <td>{{ $entry->reference_type }} #{{ $entry->reference_id }}</td>
                  <td class="text-end">
                    @if($entry->type == 'debit')
                      <span class="text-red">₹ {{ number_format($entry->amount, 2) }}</span>
                    @else
                      -
                    @endif
                  </td>
                  <td class="text-end">
                    @if($entry->type == 'credit')
                      <span class="text-green">₹ {{ number_format($entry->amount, 2) }}</span>
                    @else
                      -
                    @endif
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center text-secondary">No ledger entries found</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="card-footer d-flex align-items-center">
            {{ $entries->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
