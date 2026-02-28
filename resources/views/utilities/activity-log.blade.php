@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color:#111;">Activity Logs</h2>
    </div>

    <!-- Search form -->
    <form method="GET" action="{{ route('utilities.logs') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search logs..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    @if($logs->count() > 0)
        <div class="table-responsive rounded shadow-sm border">
        <table class="table align-middle mb-0" style="background:#fff;">
            <thead style="background:#f3f4f6;">
                <tr>
                    <th>#</th>
                    <th>Staff/Admin</th>
                    <th>Role</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $counter = ($logs->currentPage() - 1) * $logs->perPage() + 1;
                @endphp

                @foreach($logs as $log)
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td>
                            @if($log->user)
                                {{ $log->user->email ?? 'System' }}
                            @else
                                System
                            @endif
                        </td>
                        <td>
                            @if($log->user)
                                <span class="badge bg-{{ $log->user->role === 'admin' ? 'danger' : 'info' }}">
                                    {{ ucfirst($log->user->role ?? 'N/A') }}
                                </span>
                            @else
                                <span class="badge bg-secondary">System</span>
                            @endif
                        </td>
                        <td>{{ $log->action ?? 'N/A' }}</td>
                        <td>{{ $log->details ?? 'No details available' }}</td>
                        <td>{{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <!-- Pagination links -->
        <div class="d-flex justify-content-center mt-4">
            {{ $logs->links() }}
        </div>
    @else
        <div class="alert alert-info rounded shadow-sm border">No activity logs found.</div>
    @endif
</div>
@endsection
