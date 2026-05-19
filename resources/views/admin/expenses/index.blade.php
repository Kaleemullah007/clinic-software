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
            {{-- Date Filter Bar --}}
            <div class="shadow-css p-3 mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-auto">
                        <label class="form-label mb-1 small fw-semibold">Date Range</label>
                        <select id="expenseDateRange" class="form-select form-select-sm" style="min-width:160px;">
                            <option value="">All Dates</option>
                            <option value="this_week">This Week</option>
                            <option value="last_week">Last Week</option>
                            <option value="this_month" selected>This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-auto d-none" id="expenseCustomDates">
                        <div class="d-flex gap-2 align-items-end">
                            <div>
                                <label class="form-label mb-1 small fw-semibold">From</label>
                                <input type="date" id="expenseDateFrom" class="form-control form-control-sm" style="min-width:140px;">
                            </div>
                            <div>
                                <label class="form-label mb-1 small fw-semibold">To</label>
                                <input type="date" id="expenseDateTo" class="form-control form-control-sm" style="min-width:140px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-auto">
                        <button id="btnApplyExpenseFilter" class="btn btn-theme btn-sm">
                            <i class="bi bi-funnel me-1"></i> Apply
                        </button>
                        <button id="btnResetExpenseFilter" class="btn btn-outline-secondary btn-sm ms-1">
                            <i class="bi bi-x-circle me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

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
    var expenseTable = $('#expensesTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route("expenses.index") }}',
            data: function (d) {
                d.date_range = $('#expenseDateRange').val();
                d.date_from  = $('#expenseDateFrom').val();
                d.date_to    = $('#expenseDateTo').val();
            }
        },
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

    // Show/hide custom date pickers
    $('#expenseDateRange').on('change', function () {
        if ($(this).val() === 'custom') {
            $('#expenseCustomDates').removeClass('d-none');
        } else {
            $('#expenseCustomDates').addClass('d-none');
        }
    });

    // Apply filter
    $('#btnApplyExpenseFilter').on('click', function () {
        expenseTable.ajax.reload();
    });

    // Reset filter
    $('#btnResetExpenseFilter').on('click', function () {
        $('#expenseDateRange').val('');
        $('#expenseDateFrom').val('');
        $('#expenseDateTo').val('');
        $('#expenseCustomDates').addClass('d-none');
        expenseTable.ajax.reload();
    });

    // Trigger initial load with "this_month" pre-selected
    expenseTable.ajax.reload();
});
</script>
@endsection
