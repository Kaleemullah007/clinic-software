@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-box-seam me-2 text-theme-color"></i>Products</h4>
            @can('products.create')
            <a href="{{ route('products.create') }}" class="btn btn-theme btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Product
            </a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="productTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Variations</th>
                            <th>Stock</th>
                            <th>Track Inv.</th>
                            <th>Status</th>
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
    #productTable thead th{background:linear-gradient(90deg,#B1083C 0%,#d13729 100%);color:#fff;border:none;}
</style>
<script>
$(function () {
    $('#productTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("products.index") }}',
        columns: [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex',    orderable: false, searchable: false, width: '50px' },
            { data: 'name',           name: 'name' },
            { data: 'price_col',      name: 'price_col',      orderable: false, searchable: false },
            { data: 'variations_col', name: 'variations_col', orderable: false, searchable: false },
            { data: 'stock_col',      name: 'stock_col',      orderable: false, searchable: false },
            { data: 'track_col',      name: 'track_col',      orderable: false, searchable: false },
            { data: 'status_badge',   name: 'status_badge',   orderable: false, searchable: false },
            { data: 'action',         name: 'action',         orderable: false, searchable: false, className: 'text-center' },
        ],
        responsive: true,
        pageLength: 25,
        language: { searchPlaceholder: 'Search products…', processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Loading…' },
    });
});
</script>
@endsection
