@extends('layouts.admin')
@section('title', 'Point of Sale — Orders')

@section('content')
<div class="page-breadcrumb d-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Point of Sale</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active">POS Orders</li>
            </ol>
        </nav>
    </div>
    @can('pos.create')
    <div class="ms-auto">
        <a href="{{ route('pos.create') }}" class="btn text-white" style="background:linear-gradient(90deg,#B1083C,#d13729)">
            <i class="bi bi-plus-circle me-1"></i> New Sale
        </a>
        @can('pos.report')
        <a href="{{ route('pos.report') }}" class="btn btn-outline-secondary ms-2">
            <i class="bi bi-bar-chart me-1"></i> Report
        </a>
        @endcan
    </div>
    @endcan
</div>

{{-- Filter bar --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            @if(auth()->user()->isSuperAdmin())
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Clinic</label>
                <select id="filterClinic" class="form-select form-select-sm">
                    <option value="">All Clinics</option>
                    @foreach($clinics as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Status</label>
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="paid">Paid</option>
                    <option value="unpaid">Unpaid</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Date</label>
                <input type="date" id="filterDate" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <button id="btnFilter" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
            <div class="col-md-2">
                <button id="btnReset" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-x-circle me-1"></i>Reset
                </button>
            </div>
        </div>
    </div>
</div>

{{-- DataTable --}}
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="posTable" class="table table-hover align-middle" style="font-size:.875rem; width:100%">
                <thead>
                    <tr style="background:linear-gradient(90deg,#B1083C,#d13729); color:#fff">
                        <th>#</th>
                        <th>Order #</th>
                        <th>Patient</th>
                        @if(auth()->user()->isSuperAdmin())<th>Clinic</th>@endif
                        <th>Items</th>
                        <th>Totals</th>
                        <th>Status</th>
                        <th>By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function () {
    var isSuperAdmin = {{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }};
    var table;

    function buildColumns() {
        var cols = [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'order_number', render: d => '<span class="fw-semibold">' + d + '</span>' },
            { data: 'patient_col', orderable: false },
        ];
        if (isSuperAdmin) cols.push({ data: 'clinic_col', orderable: false });
        cols.push(
            { data: 'items_count', className: 'text-center' },
            { data: 'totals_col', orderable: false },
            { data: 'status_col', orderable: false, className: 'text-center' },
            { data: 'creator', render: d => d?.name ?? '—' },
            { data: 'created_at', render: d => d ? new Date(d).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : '—' },
            { data: 'action', orderable: false, searchable: false, className: 'text-center' }
        );
        return cols;
    }

    function initTable() {
        if (table) table.destroy();
        table = $('#posTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("pos.index") }}',
                data: function(d) {
                    d.filter_clinic  = $('#filterClinic').val();
                    d.filter_status  = $('#filterStatus').val();
                    d.filter_date    = $('#filterDate').val();
                }
            },
            columns: buildColumns(),
            order: [[isSuperAdmin ? 8 : 7, 'desc']],
            pageLength: 25,
            language: { processing: '<div class="spinner-border spinner-border-sm text-danger"></div>' }
        });
    }

    initTable();

    $('#btnFilter').on('click', function () { table.ajax.reload(); });
    $('#btnReset').on('click', function () {
        $('#filterClinic').val('');
        $('#filterStatus').val('');
        $('#filterDate').val('');
        table.ajax.reload();
    });

    // Toggle payment status — with SweetAlert confirmation
    $(document).on('click', '.btn-toggle-payment', function () {
        var id            = $(this).data('id');
        var isPaid        = $(this).find('.badge').hasClass('bg-success');
        var newStatus     = isPaid ? 'unpaid' : 'paid';
        var newLabel      = isPaid ? 'Unpaid' : 'Paid';
        var confirmColor  = isPaid ? '#f59e0b' : '#10b981';

        Swal.fire({
            title: 'Change payment status?',
            html:  'Mark this order as <strong>' + newLabel + '</strong>?',
            icon:  'question',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor:  '#6b7280',
            confirmButtonText:  'Yes, mark ' + newLabel,
            cancelButtonText:   'Cancel',
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.post('/pos/' + id + '/toggle-payment', { _token: '{{ csrf_token() }}' }, function (res) {
                if (res.success) {
                    table.ajax.reload(null, false);
                }
            });
        });
    });

    // Delete order
    $(document).on('click', '.btn-del-order', function () {
        var id  = $(this).data('id');
        Swal.fire({
            title: 'Delete this POS order?',
            text:  'Inventory will be restored.',
            icon:  'warning',
            showCancelButton: true,
            confirmButtonColor: '#B1083C',
            confirmButtonText: 'Yes, delete',
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '/pos/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Deleted!', 'Order removed and inventory restored.', 'success');
                        table.ajax.reload(null, false);
                    }
                },
                error: function() { Swal.fire('Error', 'Could not delete order.', 'error'); }
            });
        });
    });
});
</script>
@endsection
