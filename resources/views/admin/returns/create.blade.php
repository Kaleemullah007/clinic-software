@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-arrow-return-left me-2 text-theme-color"></i>Process Return</h4>
            <a href="{{ route('returns.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-6 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('returns.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Appointment Product ID <span class="text-danger">*</span></label>
                        <input type="number" name="appointment_product_id" class="form-control border-secondary" required
                               placeholder="Enter appointment product ID">
                        <small class="text-muted">Find from the appointment's product list</small>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control border-secondary" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Refund Amount</label>
                            <input type="number" name="refund_amount" class="form-control border-secondary" step="0.01" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Return To <span class="text-danger">*</span></label>
                            <select name="return_to" class="form-select border-secondary" required>
                                <option value="inventory">Back to Inventory</option>
                                <option value="damaged">Mark as Damaged</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Reason</label>
                            <textarea name="reason" class="form-control border-secondary" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Process Return</button>
                        <a href="{{ route('returns.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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
