@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h1 class="h2 mb-0">Import Teachers</h1>
        <a href="{{ route('teachers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Teachers
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h4 class="card-title mb-4">Import Teachers from CSV</h4>

            @if (session('import_summary'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <strong>Import Complete!</strong>
                    <ul class="mb-2 mt-2">
                        <li><strong>Imported:</strong> {{ session('import_summary.imported') }} teacher(s)</li>
                        @if(session('import_summary.errors'))
                            <li><strong>Errors:</strong> {{ count(session('import_summary.errors')) }}</li>
                        @endif
                    </ul>
                    @if(session('import_summary.errors'))
                        <details class="mt-3">
                            <summary class="cursor-pointer">View Errors</summary>
                            <ul class="mt-2 mb-0">
                                @foreach(session('import_summary.errors') as $error)
                                    <li class="text-danger small">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </details>
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row mb-5">
                <div class="col-md-6">
                    <form action="{{ route('teachers.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Select CSV File</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" name="file" accept=".csv,.txt" required>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-2"></i>Import Teachers
                        </button>
                    </form>
                </div>

                <div class="col-md-6">
                    <h5 class="mb-3">CSV Format</h5>
                    <p class="text-muted small">Your CSV file should have the following columns (in order):</p>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Column</th>
                                <th>Required</th>
                                <th>Example</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Name</td>
                                <td><span class="badge bg-danger">Required</span></td>
                                <td>John Doe</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><span class="badge bg-danger">Required</span></td>
                                <td>john@example.com</td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td><span class="badge bg-success">Optional</span></td>
                                <td>Male/Female</td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td><span class="badge bg-success">Optional</span></td>
                                <td>123 Main Street</td>
                            </tr>
                            <tr>
                                <td>Phone Number</td>
                                <td><span class="badge bg-success">Optional</span></td>
                                <td>+1234567890</td>
                            </tr>
                        </tbody>
                    </table>

                    <h5 class="mt-4 mb-3">Sample CSV</h5>
                    <div class="bg-light p-3 rounded border">
                        <code class="text-dark small d-block" style="white-space: pre-wrap;">Name,Email,Gender,Address,Phone Number
John Doe,john@example.com,Male,123 Main St,09123456789
Jane Smith,jane@example.com,Female,456 Oak Ave,09234567890
Robert Wilson,robert@example.com,Male,789 Pine Rd,09345678901</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
