@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-plus-circle me-2 text-theme-color"></i>Add Expense</h4>
            <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-6 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('expenses.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control border-secondary" value="{{ old('title') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category</label>
                            <input type="text" name="category" class="form-control border-secondary" value="{{ old('category') }}"
                                   list="catList" placeholder="e.g. Rent, Utilities">
                            <datalist id="catList">
                                <option value="Rent"><option value="Utilities"><option value="Supplies">
                                <option value="Salaries"><option value="Marketing"><option value="Other">
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Clinic</label>
                            <select name="clinic_id" class="form-select border-secondary">
                                <option value="">General</option>
                                @foreach($clinics as $c)
                                <option value="{{ $c->id }}" {{ old('clinic_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Amount (PKR) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control border-secondary" step="0.01" min="0.01" value="{{ old('amount') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control border-secondary" value="{{ old('expense_date', today()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Method</label>
                            <select name="payment_method" class="form-select border-secondary">
                                <option value="">— Select —</option>
                                <option value="cash" {{ old('payment_method')=='cash'?'selected':'' }}>Cash</option>
                                <option value="bank" {{ old('payment_method')=='bank'?'selected':'' }}>Bank Transfer</option>
                                <option value="card" {{ old('payment_method')=='card'?'selected':'' }}>Card</option>
                                <option value="cheque" {{ old('payment_method')=='cheque'?'selected':'' }}>Cheque</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reference #</label>
                            <input type="text" name="reference_number" class="form-control border-secondary" value="{{ old('reference_number') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control border-secondary" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Save</button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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
