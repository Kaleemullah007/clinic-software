@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-truck me-2 text-theme-color"></i>Vendors</h4>
            @can('vendors.create')
            <button class="btn btn-theme btn-sm" data-bs-toggle="modal" data-bs-target="#addVendorModal">
                <i class="bi bi-plus-lg me-1"></i> Add Vendor
            </button>
            @endcan
        </div>
        <hr class="my-2">
    </div>

    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="vendorTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Vendor Modal --}}
@can('vendors.create')
<div class="modal fade" id="addVendorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2 text-theme-color"></i>Add Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addVendorForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control border-secondary" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Company</label>
                            <input type="text" name="company" class="form-control border-secondary">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control border-secondary" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control border-secondary">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control border-secondary" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" value="1" id="vendorStatus" checked>
                                <label class="form-check-label" for="vendorStatus">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@section('script')
<style>
    .text-theme-color { color:#B1083C; }
    .btn-theme { background:linear-gradient(90deg,#B1083C,#d13729); color:#fff; border:none; }
    .shadow-css { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.08); }
    #vendorTable thead th { background:linear-gradient(90deg,#B1083C 0%,#d13729 100%); color:#fff; border:none; }
</style>
<script>
$(function () {
    var table = $('#vendorTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("vendor.index") }}',
        columns: [
            { data: 'DT_RowIndex',  name: 'DT_RowIndex',  orderable: false, searchable: false, width: '50px' },
            { data: 'name',         name: 'name' },
            { data: 'company',      name: 'company' },
            { data: 'phone',        name: 'phone' },
            { data: 'email',        name: 'email' },
            { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
            { data: 'action',       name: 'action',       orderable: false, searchable: false, className: 'text-center' },
        ],
        responsive: true,
        pageLength: 25,
        language: { searchPlaceholder: 'Search vendors…', processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Loading…' },
    });

    $('#addVendorForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("vendor.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    $('#addVendorModal').modal('hide');
                    table.ajax.reload();
                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON?.errors;
                if (errors) alert(Object.values(errors).flat().join('\n'));
            }
        });
    });
});
</script>
@endsection
