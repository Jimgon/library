@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color:#111;">Archive</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3 border-0" id="archiveTabs" role="tablist" style="gap:0.5rem;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 py-2 fw-semibold" id="books-tab" data-bs-toggle="tab" data-bs-target="#books" type="button" role="tab" style="border-radius:0.375rem 0.375rem 0 0;background:#f3f4f6;color:#111;">Books</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 fw-semibold" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers" type="button" role="tab" style="border-radius:0.375rem 0.375rem 0 0;background:#f3f4f6;color:#111;">Teachers</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 fw-semibold" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab" style="border-radius:0.375rem 0.375rem 0 0;background:#f3f4f6;color:#111;">Students</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 fw-semibold" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button" role="tab" style="border-radius:0.375rem 0.375rem 0 0;background:#f3f4f6;color:#111;">Staff</button>
        </li>
        
    </ul>

    <div class="tab-content bg-white p-4 rounded shadow-sm border" id="archiveTabsContent" style="min-height:350px;">
        <!-- Books Tab -->
        <div class="tab-pane fade show active" id="books" role="tabpanel">
            @include('utilities.archive-table', ['items' => $books, 'type' => 'book'])
        </div>

        <!-- Teachers Tab -->
        <div class="tab-pane fade" id="teachers" role="tabpanel">
            @include('utilities.archive-table', ['items' => $teachers ?? collect(), 'type' => 'teacher'])
        </div>

        <!-- Students Tab -->
        <div class="tab-pane fade" id="students" role="tabpanel">
            @include('utilities.archive-table', ['items' => $students, 'type' => 'student'])
        </div>

        <!-- Staff Tab -->
        <div class="tab-pane fade" id="staff" role="tabpanel">
            @include('utilities.archive-table', ['items' => $staff, 'type' => 'staff'])
        </div>
    </div>
</div>
@endsection
