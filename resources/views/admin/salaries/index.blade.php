@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-wallet2 me-2 text-theme-color"></i>Salaries</h4>
            @can('salaries.create')
            <a href="{{ route('salaries.create') }}" class="btn btn-theme btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Generate Salary
            </a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="salariesTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Staff</th>
                            <th>Period</th>
                            <th>Net Salary</th>
                            <th>Status</th>
                            <th>Processed By</th>
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
    #salariesTable thead th{background:linear-gradient(90deg,#B1083C 0%,#d13729 100%);color:#fff;border:none;}
</style>
<script>
$(function () {
    $('#salariesTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("salaries.index") }}',
        columns: [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex',    orderable: false, searchable: false, width: '50px' },
            { data: 'staff_col',      name: 'staff_col',      orderable: false },
            { data: 'period_col',     name: 'period_col',     orderable: false, searchable: false },
            { data: 'amount_col',     name: 'amount_col',     orderable: false, searchable: false },
            { data: 'status_badge',   name: 'status_badge',   orderable: false, searchable: false },
            { data: 'processor_col',  name: 'processor_col',  orderable: false, searchable: false },
            { data: 'action',         name: 'action',         orderable: false, searchable: false, className: 'text-center' },
        ],
        responsive: true,
        pageLength: 15,
        language: { searchPlaceholder: 'Search salaries…', processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Loading…' },
    });
});
</script>
@endsection
