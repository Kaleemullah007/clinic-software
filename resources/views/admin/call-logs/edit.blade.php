@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-pencil-square me-2 text-theme-color"></i>Edit Call Log</h4>
            <a href="{{ route('call-logs.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-6 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('call-logs.update', $callLog) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <p class="mb-1"><strong>Patient:</strong> {{ $callLog->patient->name ?? '—' }}</p>
                            <p class="text-muted"><strong>Appointment:</strong> {{ $callLog->appointment->appointment_id ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Call Type <span class="text-danger">*</span></label>
                            <select name="call_type" class="form-select border-secondary" required>
                                @foreach(['reminder'=>'Reminder','follow_up'=>'Follow Up','reschedule'=>'Reschedule','other'=>'Other'] as $k=>$v)
                                <option value="{{ $k }}" {{ old('call_type',$callLog->call_type)==$k?'selected':'' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Call Status <span class="text-danger">*</span></label>
                            <select name="call_status" class="form-select border-secondary" required>
                                @foreach(['answered'=>'Answered','no_answer'=>'No Answer','busy'=>'Busy','scheduled'=>'Scheduled'] as $k=>$v)
                                <option value="{{ $k }}" {{ old('call_status',$callLog->call_status)==$k?'selected':'' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Call Date & Time</label>
                            <input type="datetime-local" name="call_at" class="form-control border-secondary"
                                   value="{{ old('call_at', $callLog->call_at?->format('Y-m-d\TH:i')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control border-secondary" rows="3">{{ old('notes', $callLog->notes) }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Update</button>
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
@endsection
