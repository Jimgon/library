@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color:#111;">Add Student</h2>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Students
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
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label for="grade_select" class="form-label">Grade</label>
                        <select id="grade_select" name="grade" class="form-select">

                            @for($g = 7; $g <= 12; $g++)
                                <option value="{{ $g }}" {{ (old('grade') == $g || (old('grade_section') && preg_match('/^\s*'. $g .'(\-|\/|\s)/', old('grade_section')))) ? 'selected' : '' }}>Grade {{ $g }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="strand_select" class="form-label">Strand</label>
                        <select id="strand_select" name="strand" class="form-select">
                            <option value="">(Not applicable)</option>
                            <option value="ABM" {{ old('strand') == 'ABM' ? 'selected' : '' }}>ABM</option>
                            <option value="GAS" {{ old('strand') == 'GAS' ? 'selected' : '' }}>GAS</option>
                            <option value="STEM" {{ old('strand') == 'STEM' ? 'selected' : '' }}>STEM</option>
                            <option value="HUMSS" {{ old('strand') == 'HUMSS' ? 'selected' : '' }}>HUMSS</option>
                            <option value="ICT" {{ old('strand') == 'ICT' ? 'selected' : '' }}>ICT</option>
                            <option value="TVL" {{ old('strand') == 'TVL' ? 'selected' : '' }}>TVL</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="section_input" class="form-label">Section</label>
                        <input type="text" class="form-control" id="section_input" name="section" placeholder="e.g., A" value="{{ old('section') ?? (old('grade_section') ? preg_replace('/^.*[\-\/\s](.*)$/', '$1', old('grade_section')) : '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="lrn" class="form-label">LRN</label>
                        <input type="text" class="form-control" id="lrn" name="lrn" value="{{ old('lrn') }}" placeholder="Learner's Reference Number"
                               inputmode="numeric" pattern="^\d{1,12}$" maxlength="12"
                               oninput="this.value = this.value.replace(/\D/g, '').slice(0,12)">
                    </div>
                </div>
                {{-- hidden combined grade_section for backend compatibility --}}
                <input type="hidden" name="grade_section" id="grade_section_hidden" value="{{ old('grade_section') }}">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                               placeholder="+63 9XXXXXXXXX"
                               pattern="^\+63\s?9\d{9}$"
                               title="Enter Philippine mobile number starting with +63 9 and 10 digits (e.g. +63 9123456789)"
                               inputmode="tel" maxlength="11"
                               oninput="this.value = this.value.replace(/[^\d+\s]/g, '')">
                    </div>
                    <div class="col-md-6">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Student</button>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const gradeSelect = document.getElementById('grade_select');
    const strandSelect = document.getElementById('strand_select');
    const sectionInput = document.getElementById('section_input');
    const hidden = document.getElementById('grade_section_hidden');
    const form = document.querySelector('form[action*="users.store"]');
    if (!form) return;

    function syncHidden() {
        const g = gradeSelect.value ? gradeSelect.value : '';
        const strand = strandSelect && strandSelect.value ? strandSelect.value : '';
        const s = sectionInput.value ? sectionInput.value.trim() : '';
        // combine grade, strand (if present for SHS), then section
        let parts = [];
        if (g) parts.push(g);
        if (strand) parts.push(strand);
        if (s) parts.push(s);
        hidden.value = parts.join('-');
    }

    // show/hide strand based on grade
    function updateStrandVisibility(){
        const g = parseInt(gradeSelect.value || 0, 10);
        if (strandSelect) {
            if (g >= 11) {
                strandSelect.parentElement.style.display = '';
            } else {
                strandSelect.value = '';
                strandSelect.parentElement.style.display = 'none';
            }
        }
    }

    // initialize hidden and visibility on load
    updateStrandVisibility();
    syncHidden();

    // update hidden when user changes controls
    gradeSelect.addEventListener('change', function(){ updateStrandVisibility(); syncHidden(); });
    if (strandSelect) strandSelect.addEventListener('change', syncHidden);
    sectionInput.addEventListener('input', syncHidden);

    // ensure hidden is set just before submit
    form.addEventListener('submit', function(){ syncHidden(); });
});
</script>
@endsection