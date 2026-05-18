@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-plus-circle me-2 text-theme-color"></i>New Consent Form</h4>
            <a href="{{ route('consent-forms.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-8 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('consent-forms.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Appointment <span class="text-danger">*</span></label>
                            <select name="appointment_id" class="form-select border-secondary" id="cfAppt" required>
                                <option value="">— Select —</option>
                                @foreach($appointments as $a)
                                <option value="{{ $a->id }}" data-patient="{{ $a->user_id }}">
                                    #{{ $a->id }} — {{ $a->patient->name ?? $a->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Patient ID <span class="text-danger">*</span></label>
                            <input type="number" name="patient_id" id="cfPatient" class="form-control border-secondary" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Form Title <span class="text-danger">*</span></label>
                            <input type="text" name="form_title" class="form-control border-secondary" value="{{ old('form_title') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Consent Text</label>
                            <textarea name="form_content" class="form-control border-secondary" rows="8">{{ old('form_content') }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Create</button>
                        <a href="{{ route('consent-forms.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<style>.text-theme-color{color:#B1083C;}.btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}.shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}</style>
<script>
$(function(){ $('#cfAppt').on('change',function(){ const pid=$(this).find(':selected').data('patient'); if(pid) $('#cfPatient').val(pid); }); });
</script>
@endsection
