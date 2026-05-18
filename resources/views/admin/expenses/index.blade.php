@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">
                <i class="bi bi-cash-stack me-2 text-theme-color"></i>Expenses
                <small class="fs-6 text-muted ms-2">
                    This Month: <span class="fw-bold text-theme-color">PKR {{ number_format($totalMonth, 2) }}</span>
                </small>
            </h4>
            @can('expenses.create')
            <a href="{{ route('expenses.create') }}" class="btn btn-theme btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Expense
            </a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="expensesTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Clinic</th>
                            <th>Category</th>
                            <th>Added By</th>
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
    .btn-outline-theme{border-color:#B1083C;color:#B1083C;}
    .btn-outline-theme:hover{background:#B1083C;color:#fff;}
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
    #expensesTable thead th{background:linear-gradient(90deg,#B1083C 0%,#d13729 100%);color:#fff;border:none;}
</style>
<script>
$(function () {
    $('#expensesTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("expenses.index") }}',
        columns: [
            { data: 'DT_RowIndex',  name: 'DT_RowIndex',  orderable: false, searchable: false, width: '50px' },
            { data: 'title',        name: 'title' },
            { data: 'amount_col',   name: 'amount_col',   orderable: false, searchable: false },
            { data: 'expense_date', name: 'expense_date' },
            { data: 'clinic_col',   name: 'clinic_col',   orderable: false },
            { data: 'category',     name: 'category' },
            { data: 'creator_col',  name: 'creator_col',  orderable: false, searchable: false },
            { data: 'action',       name: 'action',       orderable: false, searchable: false, className: 'text-center' },
        ],
        order: [[3, 'desc']],
        responsive: true,
        pageLength: 15,
        language: { searchPlaceholder: 'Search expenses…', processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Loading…' },
    });
});
</script>
@endsection
