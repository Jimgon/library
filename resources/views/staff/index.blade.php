@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1">Staff Management</h4>
            <p class="text-muted mb-0">Manage library staff accounts and permissions</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Add Staff
            </a>
        </div>
    </div>

    {{-- Success Notification --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Error Notification --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    {{-- Search Form --}}
    <form class="row g-3 mb-4" action="{{ route('staff.index') }}" method="GET">
        <div class="col-md-8">
            <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Search staff by email or role..." onchange="this.form.submit()">
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary w-100" type="submit">
                <i class="bi bi-search me-1"></i>Search
            </button>
        </div>
    </form>

    {{-- Staff Table --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Staff Accounts</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-semibold">Email</th>
                            <th class="border-0 fw-semibold">Role</th>
                            <th class="border-0 fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $user->email ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-secondary">{{ ucfirst($user->role ?? 'N/A') }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('staff.edit', $user->id) }}" class="btn btn-sm btn-outline-dark" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('staff.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this staff account? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                        No staff accounts found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-center mt-4 p-3">
                    {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush

