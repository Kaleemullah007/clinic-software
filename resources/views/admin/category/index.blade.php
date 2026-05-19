@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">Services</h4>
            @can('categories.create')
            <a href="{{ route('category.create') }}" class="btn btn-sm btn-success">
                <i class="bi bi-plus-circle me-1"></i> Create
            </a>
            @endcan
        </div>
        <hr class="my-2">
    </div>

    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="servicesTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Total Services</th>
                            <th>Status</th>
                            <th>Action</th>
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
    .shadow-css { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.08); }
    #servicesTable thead th {
        background: #B1083C !important;
        color: #fff !important;
        border-color: #9a072f !important;
        white-space: nowrap;
    }
    #servicesTable thead .sorting_asc,
    #servicesTable thead .sorting_desc { background: #8e0630 !important; }
</style>
<script>
$(function () {
    $('#servicesTable').DataTable({
        serverSide : true,
        processing : true,
        ajax       : '{{ route("category.index") }}',
        columns    : [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex',    orderable: false, searchable: false, width: '50px' },
            { data: 'name',           name: 'name' },
            { data: 'price_col',      name: 'price',          searchable: false },
            { data: 'total_services', name: 'total_services',  searchable: false },
            { data: 'status_col',     name: 'status',         orderable: false, searchable: false },
            { data: 'action',         name: 'action',         orderable: false, searchable: false, className: 'text-center' },
        ],
        order      : [[1, 'asc']],
        responsive : true,
        pageLength : 15,
        language   : {
            searchPlaceholder : 'Search services…',
            processing        : '<div class="spinner-border spinner-border-sm" style="color:#B1083C"></div> Loading…',
        },
        drawCallback: function () {
            // Re-init bootstrap-toggle checkboxes after each DataTables draw
            $('[data-toggle="toggle"]').bootstrapToggle();
        }
    });
});
</script>
@endsection
