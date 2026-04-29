@extends('layouts.tabler')
@section('title', 'Tally Sync Management')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-primary" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                    Tally Integration
                </h2>
                <p class="page-subtitle">Sync transactions to Tally ERP</p>
            </div>
            <div class="col-auto d-flex gap-2">
                <form method="POST" action="{{ route('erp.tally.sync-all') }}">
                    @csrf
                    <button class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/></svg>
                        Sync All Pending
                    </button>
                </form>
                <form method="POST" action="{{ route('erp.tally.retry-failed') }}">
                    @csrf
                    <button class="btn btn-outline-warning">Retry Failed</button>
                </form>
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#settingsModal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /></svg>
                    Settings
                </button>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        @if(session('success'))<div class="alert alert-success alert-dismissible mb-3">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
        @if(session('error'))<div class="alert alert-danger alert-dismissible mb-3">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

        {{-- Summary Stats --}}
        <div class="row row-cards mb-3">
            <div class="col-sm-4">
                <div class="card card-sm bg-yellow-lt border-warning border-2">
                    <div class="card-body text-center">
                        <div class="h1 fw-bold text-warning">{{ $summary['pending'] }}</div>
                        <div class="text-muted">Pending</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card card-sm bg-green-lt border-success border-2">
                    <div class="card-body text-center">
                        <div class="h1 fw-bold text-success">{{ $summary['success'] }}</div>
                        <div class="text-muted">Synced</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card card-sm bg-red-lt border-danger border-2">
                    <div class="card-body text-center">
                        <div class="h1 fw-bold text-danger">{{ $summary['failed'] }}</div>
                        <div class="text-muted">Failed</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Current Mode Banner --}}
        @php $mode = $settings['tally_sync_mode']->value ?? 'manual'; @endphp
        <div class="alert alert-info mb-3">
            <strong>Sync Mode:</strong>
            @if($mode === 'instant') <span class="badge bg-green">⚡ Instant</span> — Transactions sync immediately via queue.
            @elseif($mode === 'scheduled') <span class="badge bg-blue">🕐 Scheduled</span> — Synced by <code>php artisan tally:sync</code> cron.
            @else <span class="badge bg-yellow">👆 Manual</span> — Click "Sync All Pending" to sync.
            @endif
        </div>

        {{-- Status Filter --}}
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    @foreach(['' => 'All', 'pending' => 'Pending', 'success' => 'Success', 'failed' => 'Failed'] as $s => $label)
                    <li class="nav-item">
                        <a href="{{ route('erp.tally.index', ['status' => $s]) }}" class="nav-link {{ $status === $s ? 'active' : '' }}">{{ $label }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr><th>ID</th><th>Reference</th><th>Voucher Type</th><th>Status</th><th>Retries</th><th>Last Attempt</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="text-muted">#{{ $log->id }}</td>
                            <td><span class="fw-medium">{{ $log->reference_type }}</span> <span class="text-muted">#{{ $log->reference_id }}</span></td>
                            <td><span class="badge bg-blue-lt text-blue">{{ $log->voucher_type }}</span></td>
                            <td>
                                @php $cls = match($log->status) { 'success'=>'bg-green','failed'=>'bg-red','pending'=>'bg-yellow',default=>'bg-secondary' }; @endphp
                                <span class="badge {{ $cls }}">{{ ucfirst($log->status) }}</span>
                            </td>
                            <td>{{ $log->retry_count }}</td>
                            <td class="text-muted">{{ $log->last_attempt_at?->diffForHumans() ?? 'Never' }}</td>
                            <td class="d-flex gap-1">
                                @if($log->status !== 'success')
                                <form method="POST" action="{{ route('erp.tally.sync', $log) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-success">Sync Now</button>
                                </form>
                                @endif
                                <a href="{{ route('erp.tally.show', $log) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                <a href="{{ route('erp.tally.xml', $log) }}" target="_blank" class="btn btn-sm btn-outline-secondary">XML</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No sync logs found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $logs->links() }}</div>
        </div>
    </div>
</div>

{{-- Settings Modal --}}
<div class="modal fade" id="settingsModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('erp.tally.settings') }}">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tally Settings</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Sync Mode</label>
                        <select name="tally_sync_mode" class="form-select" required>
                            @foreach(['manual'=>'Manual','scheduled'=>'Scheduled (Cron)','instant'=>'Instant (Queue)'] as $v => $l)
                            <option value="{{ $v }}" {{ ($settings['tally_sync_mode']->value ?? 'manual') === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Tally URL</label>
                        <input type="url" name="tally_url" class="form-control" value="{{ $settings['tally_url']->value ?? 'http://localhost:9000' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Company Name in Tally</label>
                        <input type="text" name="tally_company" class="form-control" value="{{ $settings['tally_company']->value ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Max Retries</label>
                        <input type="number" name="tally_max_retries" class="form-control" min="1" max="10" value="{{ $settings['tally_max_retries']->value ?? 3 }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
