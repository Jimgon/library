@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Database Backups</h1>
        <form id="backupForm" action="{{ route('utilities.backup') }}" method="POST" style="margin-bottom:0;">
            @csrf
            <button type="submit" class="btn btn-dark">
                <i class="fas fa-plus"></i> Create New Backup
            </button>
        </form>
    </div>
    <div class="card">
        <div class="card-body">
            @if(count($backups) > 0)
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Size</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                            <tr>
                                <td>{{ $backup['name'] }}</td>
                                <td>{{ number_format($backup['size'] / 1024, 2) }} KB</td>
                                <td>{{ $backup['date'] }}</td>
                                <td>
                                    <a href="{{ route('utilities.downloadBackup', $backup['name']) }}" class="btn btn-primary btn-sm">
                                        Download
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-info mb-0">No backup files found.</div>
            @endif
        </div>
    </div>
</div>
<script>
    const backupForm = document.getElementById('backupForm');
    if (backupForm) {
        backupForm.addEventListener('submit', function(e) {
            if(!confirm("Are you sure you want to create a new backup?")) {
                e.preventDefault();
            }
        });
    }
</script>
@endsection
