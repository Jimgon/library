@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Add New Staff</h3>

    <!-- Display validation errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('staff.store') }}" method="POST">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input 
                type="email" 
                name="email" 
                class="form-control" 
                value="{{ old('email') }}" 
                required
            >
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input 
                type="password" 
                name="password" 
                class="form-control" 
                required
            >
        </div>

        <!-- Role -->
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-control" required>
                
                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="mt-3">
            <button type="submit" class="btn btn-dark">Add</button>
            <a href="{{ route('staff.index') }}" class="btn btn-secondary ms-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
