@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-telephone-plus me-2 text-theme-color"></i>Log Call</h4>
            <a href="{{ route('call-logs.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-6 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('call-logs.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Appointment (optional)</label>
                            <select name="appointment_id" class="form-select border-secondary" id="apptSelect">
                                <option value="">— Select Appointment —</option>
                                @foreach($appointments as $a)
                                <option value="{{ $a->id }}" data-patient="{{ $a->user_id }}">
                                    #{{ $a->id }} — {{ $a->patient->name ?? $a->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Patient ID <span class="text-danger">*</span></label>
                            <input type="number" name="patient_id" id="patientId" class="form-control border-secondary" required value="{{ old('patient_id') }}">
                            <small class="text-muted">Auto-filled when appointment selected</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Call Type <span class="text-danger">*</span></label>
                            <select name="call_type" class="form-select border-secondary" required>
                                <option value="reminder" {{ old('call_type')=='reminder'?'selected':'' }}>Reminder</option>
                                <option value="follow_up" {{ old('call_type')=='follow_up'?'selected':'' }}>Follow Up</option>
                                <option value="reschedule" {{ old('call_type')=='reschedule'?'selected':'' }}>Reschedule</option>
                                <option value="other" {{ old('call_type')=='other'?'selected':'' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Call Status <span class="text-danger">*</span></label>
                            <select name="call_status" class="form-select border-secondary" required>
                                <option value="answered" {{ old('call_status')=='answered'?'selected':'' }}>Answered</option>
                                <option value="no_answer" {{ old('call_status')=='no_answer'?'selected':'' }}>No Answer</option>
                                <option value="busy" {{ old('call_status')=='busy'?'selected':'' }}>Busy</option>
                                <option value="scheduled" {{ old('call_status')=='scheduled'?'selected':'' }}>Scheduled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Call Date & Time</label>
                            <input type="datetime-local" name="call_at" class="form-control border-secondary" value="{{ old('call_at') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control border-secondary" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Save</button>
                        <a href="{{ route('call-logs.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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
$(function(){
    $('#apptSelect').on('change', function(){
        const pid = $(this).find(':selected').data('patient');
        if(pid) $('#patientId').val(pid);
    });
});
</script>
@endsection
