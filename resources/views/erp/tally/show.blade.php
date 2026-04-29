@extends('layouts.tabler')
@section('title', 'Tally Sync Log #' . $log->id)
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('erp.tally.index') }}">Tally Sync</a></li>
                    <li class="breadcrumb-item active">Log #{{ $log->id }}</li>
                </ol>
                <h2 class="page-title">Tally Sync Log #{{ $log->id }}</h2>
            </div>
            <div class="col-auto d-flex gap-2">
                @if($log->status !== 'success')
                <form method="POST" action="{{ route('erp.tally.sync', $log) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-success">Sync Now</button>
                </form>
                @endif
                <a href="{{ route('erp.tally.xml', $log) }}" target="_blank" class="btn btn-outline-secondary">View XML</a>
                <a href="{{ route('erp.tally.index') }}" class="btn btn-ghost">← Back</a>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Log Details</h3></div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-5">Reference</dt>
                            <dd class="col-7">{{ $log->reference_type }} #{{ $log->reference_id }}</dd>
                            <dt class="col-5">Voucher Type</dt>
                            <dd class="col-7"><span class="badge bg-blue-lt text-blue">{{ $log->voucher_type }}</span></dd>
                            <dt class="col-5">Status</dt>
                            <dd class="col-7">
                                @php $cls = match($log->status) {'success'=>'bg-green','failed'=>'bg-red','pending'=>'bg-yellow',default=>'bg-secondary'}; @endphp
                                <span class="badge {{ $cls }}">{{ ucfirst($log->status) }}</span>
                            </dd>
                            <dt class="col-5">Retry Count</dt>
                            <dd class="col-7">{{ $log->retry_count }}</dd>
                            <dt class="col-5">Last Attempt</dt>
                            <dd class="col-7">{{ $log->last_attempt_at?->format('d M Y H:i:s') ?? 'Never' }}</dd>
                            <dt class="col-5">Synced At</dt>
                            <dd class="col-7">{{ $log->synced_at?->format('d M Y H:i:s') ?? '-' }}</dd>
                            @if($log->error_message)
                            <dt class="col-5">Error</dt>
                            <dd class="col-7 text-danger">{{ $log->error_message }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                @if($log->response)
                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title">Tally Response</h3></div>
                    <div class="card-body">
                        <pre class="bg-light p-3 rounded" style="max-height:300px;overflow:auto;font-size:12px;">{{ $log->response }}</pre>
                    </div>
                </div>
                @endif
                <div class="card">
                    <div class="card-header"><h3 class="card-title">XML Payload</h3></div>
                    <div class="card-body">
                        <pre class="bg-dark text-success p-3 rounded" style="max-height:400px;overflow:auto;font-size:11px;">{{ htmlspecialchars($log->payload) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
