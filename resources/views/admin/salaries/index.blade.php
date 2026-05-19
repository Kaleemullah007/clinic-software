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
            {{-- Date Filter Bar --}}
            <div class="shadow-css p-3 mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-auto">
                        <label class="form-label mb-1 small fw-semibold">Date Range</label>
                        <select id="salaryDateRange" class="form-select form-select-sm" style="min-width:160px;">
                            <option value="">All Records</option>
                            <option value="this_week">This Week</option>
                            <option value="last_week">Last Week</option>
                            <option value="this_month" selected>This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-auto d-none" id="salaryCustomDates">
                        <div class="d-flex gap-2 align-items-end">
                            <div>
                                <label class="form-label mb-1 small fw-semibold">From</label>
                                <input type="date" id="salaryDateFrom" class="form-control form-control-sm" style="min-width:140px;">
                            </div>
                            <div>
                                <label class="form-label mb-1 small fw-semibold">To</label>
                                <input type="date" id="salaryDateTo" class="form-control form-control-sm" style="min-width:140px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-auto">
                        <button id="btnApplySalaryFilter" class="btn btn-theme btn-sm">
                            <i class="bi bi-funnel me-1"></i> Apply
                        </button>
                        <button id="btnResetSalaryFilter" class="btn btn-outline-secondary btn-sm ms-1">
                            <i class="bi bi-x-circle me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

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
    var salaryTable = $('#salariesTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route("salaries.index") }}',
            data: function (d) {
                d.date_range = $('#salaryDateRange').val();
                d.date_from  = $('#salaryDateFrom').val();
                d.date_to    = $('#salaryDateTo').val();
            }
        },
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

    // Show/hide custom date pickers
    $('#salaryDateRange').on('change', function () {
        if ($(this).val() === 'custom') {
            $('#salaryCustomDates').removeClass('d-none');
        } else {
            $('#salaryCustomDates').addClass('d-none');
        }
    });

    // Apply filter
    $('#btnApplySalaryFilter').on('click', function () {
        salaryTable.ajax.reload();
    });

    // Reset filter
    $('#btnResetSalaryFilter').on('click', function () {
        $('#salaryDateRange').val('');
        $('#salaryDateFrom').val('');
        $('#salaryDateTo').val('');
        $('#salaryCustomDates').addClass('d-none');
        salaryTable.ajax.reload();
    });

    // Trigger initial load with "this_month" pre-selected
    salaryTable.ajax.reload();
});
</script>
@endsection
