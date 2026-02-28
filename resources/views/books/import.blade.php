@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Import Books</h2>
    <div class="alert alert-info">
        <strong>CSV Format:</strong> Upload a CSV file with the following columns in order:
        <ol class="mb-0 mt-2">
            <li>Title (required)</li>
            <li>Author (required)</li>
            <li>Publisher (optional)</li>
            <li>ISBN (required, must be unique)</li>
            <li>Category (required)</li>
            <li>Copies (required)</li>
        </ol>
    </div>
    <form action="{{ route('books.import.post') }}" method="POST" enctype="multipart/form-data" class="mt-3">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">Choose file (CSV, XLSX, XLS):</label>
            <input type="file" name="file" id="file" class="form-control" accept=".csv,.txt" required>
            @error('file')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">
            <i class="bi bi-upload"></i> Import
        </button>
        <a href="{{ route('books.index') }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
@endsection
