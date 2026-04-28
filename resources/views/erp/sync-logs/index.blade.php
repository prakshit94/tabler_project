@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Sync Logs</h2>
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
              <th>Date</th>
              <th>Module</th>
              <th>Action</th>
              <th>Status</th>
              <th>Message</th>
            </tr>
          </thead>
          <tbody>
            @forelse($logs as $log)
            <tr>
              <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
              <td>{{ ucfirst($log->module) }}</td>
              <td>{{ ucfirst($log->action) }}</td>
              <td>
                <span class="badge {{ $log->status == 'success' ? 'bg-green-lt' : 'bg-red-lt' }}">
                  {{ ucfirst($log->status) }}
                </span>
              </td>
              <td class="text-truncate" style="max-width: 300px;">{{ $log->message }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center text-secondary">No sync logs found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex align-items-center">
        {{ $logs->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
