@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Utilities</h1>
    </div>

    <div class="row">
        <!-- Activity Logs -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5>Activity Logs</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('utilities.logs') }}" class="btn btn-primary w-100">
                        View Logs
                    </a>
                </div>
            </div>
        </div>

        <!-- Archive Collection -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5>Archive</h5>
                </div>
                <div class="card-body">
                    <!-- Changed from POST form to GET link -->
                    <a href="{{ route('utilities.archive') }}" class="btn btn-warning w-100">
                        View Archive
                    </a>
                </div>
            </div>
        </div>

        <!-- Backup Database -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5>Backup Database</h5>
                </div>
                <div class="card-body">
                    <form id="backupForm" action="{{ route('utilities.backup') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            Backup Database
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const backupForm = document.getElementById('backupForm');
    backupForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if(confirm("Are you sure you want to backup the entire database?")) {
            backupForm.submit();
        }
    });
</script>
@endpush
