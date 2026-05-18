@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-plus-circle me-2 text-theme-color"></i>Generate Salary</h4>
            <a href="{{ route('salaries.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-6 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('salaries.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Employee <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select border-secondary" id="empSelect" required>
                                <option value="">— Select —</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}" data-salary="{{ $u->salary_amount }}" {{ old('user_id')==$u->id?'selected':'' }}>
                                    {{ $u->name }} ({{ ucfirst($u->salary_type) }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Month <span class="text-danger">*</span></label>
                            <select name="month" class="form-select border-secondary" required>
                                @foreach($months as $m)
                                <option value="{{ $m }}" {{ old('month', now()->month)==$m?'selected':'' }}>
                                    {{ \Carbon\Carbon::create(null,$m)->format('F') }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
                            <select name="year" class="form-select border-secondary" required>
                                @foreach($years as $y)
                                <option value="{{ $y }}" {{ old('year', now()->year)==$y?'selected':'' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Basic Salary <span class="text-danger">*</span></label>
                            <input type="number" name="basic_salary" id="basicSalary" class="form-control border-secondary" step="0.01" min="0" value="{{ old('basic_salary') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bonus</label>
                            <input type="number" name="bonus" class="form-control border-secondary" step="0.01" min="0" value="{{ old('bonus',0) }}" id="bonusField">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Deductions</label>
                            <input type="number" name="deductions" class="form-control border-secondary" step="0.01" min="0" value="{{ old('deductions',0) }}" id="deductField">
                        </div>
                        <div class="col-12">
                            <div class="alert alert-light border fw-semibold">
                                Net Salary: <span id="netSalary" class="text-theme-color">PKR 0.00</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control border-secondary" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Save</button>
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
function calcNet(){
    const b=parseFloat($('#basicSalary').val())||0;
    const bo=parseFloat($('#bonusField').val())||0;
    const d=parseFloat($('#deductField').val())||0;
    $('#netSalary').text('PKR '+(b+bo-d).toFixed(2));
}
$(function(){
    $('#empSelect').on('change',function(){
        const sal=$(this).find(':selected').data('salary')||0;
        $('#basicSalary').val(sal);
        calcNet();
    });
    $('#basicSalary,#bonusField,#deductField').on('input',calcNet);
    calcNet();
});
</script>
@endsection
