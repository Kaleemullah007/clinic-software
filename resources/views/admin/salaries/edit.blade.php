@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-pencil-square me-2 text-theme-color"></i>Edit Salary</h4>
            <a href="{{ route('salaries.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-6 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('salaries.update', $salary) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <p class="mb-0"><strong>Employee:</strong> {{ $salary->user->name }}</p>
                            <p class="text-muted"><strong>Period:</strong> {{ \Carbon\Carbon::create($salary->year,$salary->month)->format('F Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Basic Salary</label>
                            <input type="number" name="basic_salary" id="basicSalary" class="form-control border-secondary" step="0.01" min="0" value="{{ old('basic_salary', $salary->basic_salary) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bonus</label>
                            <input type="number" name="bonus" class="form-control border-secondary" id="bonusField" step="0.01" min="0" value="{{ old('bonus', $salary->bonus) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Deductions</label>
                            <input type="number" name="deductions" class="form-control border-secondary" id="deductField" step="0.01" min="0" value="{{ old('deductions', $salary->deductions) }}">
                        </div>
                        <div class="col-12">
                            <div class="alert alert-light border fw-semibold">
                                Net Salary: <span id="netSalary" class="text-theme-color">PKR {{ number_format($salary->net_salary,2) }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control border-secondary" rows="2">{{ old('notes', $salary->notes) }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Update</button>
                        <a href="{{ route('salaries.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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
function calcNet(){ const b=parseFloat($('#basicSalary').val())||0,bo=parseFloat($('#bonusField').val())||0,d=parseFloat($('#deductField').val())||0; $('#netSalary').text('PKR '+(b+bo-d).toFixed(2)); }
$(function(){ $('#basicSalary,#bonusField,#deductField').on('input',calcNet); });
</script>
@endsection
