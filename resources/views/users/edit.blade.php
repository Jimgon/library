@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color:#111;">
            @if(request()->routeIs('teachers.edit'))
                Edit Teacher
            @else
                Edit Student
            @endif
        </h2>
        <a href="
            @if(request()->routeIs('teachers.edit'))
                {{ route('teachers.index') }}
            @else
                {{ route('users.index') }}
            @endif
        " class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="
                @if(request()->routeIs('teachers.edit'))
                    {{ route('teachers.update', $teacher->id) }}
                @else
                    {{ route('users.update', $teacher->id) }}
                @endif
            ">
                @csrf
                @method('PUT')
                @if(request()->routeIs('teachers.edit'))
                    <!-- Teacher Form -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $teacher->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $teacher->email) }}" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $teacher->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $teacher->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $teacher->address) }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $teacher->phone_number) }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Teacher</button>
                @else
                    <!-- Student Form -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $teacher->first_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $teacher->last_name) }}" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="grade_section" class="form-label">Grade & Section</label>
                            <input type="text" class="form-control" id="grade_section" name="grade_section" value="{{ old('grade_section', $teacher->grade_section) }}" placeholder="e.g., 10-A">
                        </div>
                        <div class="col-md-6">
                            <label for="lrn" class="form-label">LRN</label>
                            <input type="text" class="form-control" id="lrn" name="lrn" value="{{ old('lrn', $teacher->lrn) }}" placeholder="Learner's Reference Number">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $teacher->phone_number) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $teacher->address) }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Student</button>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
