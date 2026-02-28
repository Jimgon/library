@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Edit Staff</h3>

    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Old password incorrect error --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('staff.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Email --}}
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input 
                type="email" 
                name="email" 
                class="form-control" 
                value="{{ old('email', $user->email) }}" 
                required
            >
        </div>

        {{-- Role --}}
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-control" required>
                <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        {{-- Password Change Section --}}
        <div class="mb-3">
            <label class="form-label">Old Password 
                <small class="text-muted">(required if changing password)</small>
            </label>
            <input 
                type="password" 
                name="old_password" 
                class="form-control" 
                placeholder="Enter old password"
            >
        </div>

        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input 
                type="password" 
                name="new_password" 
                class="form-control" 
                placeholder="Enter new password"
            >
        </div>

        {{-- Form Buttons --}}
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Update Staff</button>
            <a href="{{ route('staff.index') }}" class="btn btn-secondary ms-2">Cancel</a>
        </div>
    </form>
</div>
@endsection

