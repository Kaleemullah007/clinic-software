@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-bag-check me-2 text-theme-color"></i>Purchases</h4>
            @can('purchases.create')
            <a href="{{ route('purchases.create') }}" class="btn btn-theme btn-sm">
                <i class="bi bi-plus-lg me-1"></i> New Purchase
            </a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="purchasesTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>PO #</th>
                            <th>Vendor</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<style>
    .text-theme-color{color:#B1083C;}
    .btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
    #purchasesTable thead th{background:linear-gradient(90deg,#B1083C 0%,#d13729 100%);color:#fff;border:none;}
</style>
<script>
$(function () {
    $('#purchasesTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("purchases.index") }}',
        columns: [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex',    orderable: false, searchable: false, width: '50px' },
            { data: 'purchase_number', name: 'purchase_number' },
            { data: 'vendor_col',     name: 'vendor_col',     orderable: false },
            { data: 'total_col',      name: 'total_col',      orderable: false, searchable: false },
            { data: 'payment_col',    name: 'payment_col',    orderable: false, searchable: false },
            { data: 'purchase_date',  name: 'purchase_date' },
            { data: 'creator_col',    name: 'creator_col',    orderable: false, searchable: false },
            { data: 'action',         name: 'action',         orderable: false, searchable: false, className: 'text-center' },
        ],
        order: [[5, 'desc']],
        responsive: true,
        pageLength: 15,
        language: { searchPlaceholder: 'Search purchases…', processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Loading…' },
    });
});
</script>
@endsection
