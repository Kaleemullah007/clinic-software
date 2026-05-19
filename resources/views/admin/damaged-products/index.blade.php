@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-exclamation-triangle me-2 text-theme-color"></i>Damaged Products</h4>
            @can('damaged-products.create')
            <a href="{{ route('damaged-products.create') }}" class="btn btn-theme btn-sm"><i class="bi bi-plus-lg me-1"></i> Report Damage</a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="damagedTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Reason</th>
                            <th>Reported By</th>
                            <th>Date</th>
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
    #damagedTable thead th{background:#B1083C!important;color:#fff!important;border-color:#9a072f!important;white-space:nowrap;}
    #damagedTable thead .sorting_asc,#damagedTable thead .sorting_desc{background:#8e0630!important;}
</style>
<script>
$(function(){
    $('#damagedTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("damaged-products.index") }}',
        columns: [
            {data:'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false, width:'50px'},
            {data:'product_name', name:'product.name'},
            {data:'quantity', name:'quantity'},
            {data:'reason_short', name:'reason', orderable:false},
            {data:'reported_by_name', name:'reportedBy.name'},
            {data:'date', name:'created_at'},
            {data:'action', name:'action', orderable:false, searchable:false, className:'text-center'},
        ],
        order: [[5, 'desc']],
        responsive: true,
        pageLength: 15,
        language: {
            searchPlaceholder: 'Search...',
            processing: '<div class="spinner-border spinner-border-sm" style="color:#B1083C"></div> Loading…'
        },
    });
});
</script>
@endsection
