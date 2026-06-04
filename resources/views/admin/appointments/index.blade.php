@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="container">

        <div class="row pt-3 align-items-center">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0"><i class="bi bi-calendar2-check me-2" style="color:#B1083C;"></i>Appointments</h4>
                @can('create', App\Models\Appointment::class)
                <a href="{{ route('appointments.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>New Appointment
                </a>
                @endcan
            </div>
            <hr class="mt-2">
        </div>

        {{-- Custom filter bar --}}
        <div class="row mb-3 g-2 align-items-end">
            <div class="col-lg-3 col-md-6 col-12">
                <select id="filterPatient" style="width:100%">
                    <option value="">All Patients</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->name }}{{ $patient->appointments_count > 0 ? ' ('.$patient->appointments_count.')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-6 col-12">
                <input type="date" id="filterDate" class="form-control border-secondary" placeholder="Filter by date">
            </div>
            <div class="col-lg-2 col-md-6 col-12">
                <select class="form-select border-secondary" id="filterStatus">
                    <option value="">All Status</option>
                    <option value="1">Paid</option>
                    <option value="0">Unpaid</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-6 col-12">
                <button type="button" id="resetFilters" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle me-1"></i>Reset
                </button>
            </div>
        </div>

        @include('flash-message')

        <div class="shadow-css p-3">
            <table id="appointmentsTable" class="table table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Services</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>

    @include('admin.prescription.create')
</div>
@endsection

@section('script')
<style>
    .shadow-css { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.08); }
    .btn-outline-theme { border-color:#B1083C; color:#B1083C; }
    .btn-outline-theme:hover { background:#B1083C; color:#fff; }
    #appointmentsTable thead th { background:linear-gradient(90deg,#B1083C 0%,#d13729 100%); color:#fff; border:none; }
    .btn-wa { background:#25d366; color:#fff; border:none; }
    .btn-wa:hover { background:#1ebe5d; color:#fff; }
    .btn-wa:disabled { background:#a8d5b5; cursor:not-allowed; }

    /* Select2 theme */
    #filterPatient + .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #adb5bd;
        border-radius: 6px;
        display: flex;
        align-items: center;
    }
    #filterPatient + .select2-container .select2-selection__rendered {
        line-height: 38px;
        padding-left: 10px;
        color: #212529;
    }
    #filterPatient + .select2-container .select2-selection__arrow {
        height: 36px;
        right: 6px;
    }
    .select2-container--default .select2-results__option--highlighted {
        background-color: #B1083C !important;
    }
    .select2-container--default .select2-results__option--selected {
        background-color: #f5c6d3 !important;
        color: #B1083C !important;
    }
    .select2-dropdown {
        border-color: #B1083C !important;
        border-radius: 6px !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #B1083C !important;
        outline: none;
    }
    .select2-container--open .select2-selection--single {
        border-color: #B1083C !important;
        box-shadow: 0 0 0 .2rem rgba(177,8,60,.2) !important;
    }
    .select2-selection__clear {
        color: #B1083C !important;
        font-size: 1.1rem;
        font-weight: bold;
        margin-right: 4px;
    }
</style>
<script>
// Everything in window.load so Select2 + DataTables are guaranteed ready
$(window).on('load', function () {

    // ── Searchable patient dropdown ──────────────────────────────────────────
    $('#filterPatient').select2({
        placeholder: 'All Patients',
        allowClear: true,
        width: '100%',
    });

    // ── DataTable ────────────────────────────────────────────────────────────
    var table = $('#appointmentsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route("appointments.index") }}',
            data: function (d) {
                d.filter_date    = $('#filterDate').val();
                d.filter_paid    = $('#filterStatus').val();
                d.filter_patient = $('#filterPatient').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex',  name: 'DT_RowIndex',  orderable: false, searchable: false, width: '50px' },
            { data: 'patient_col',  name: 'patient_col',  orderable: false },
            { data: 'doctor_col',   name: 'doctor_col',   orderable: false, searchable: false },
            { data: 'date',         name: 'date' },
            { data: 'service_col',  name: 'service_col',  orderable: false, searchable: false },
            { data: 'amount_col',   name: 'amount_col',   orderable: false, searchable: false },
            { data: 'paid_col',     name: 'paid_col',     orderable: false, searchable: false },
            { data: 'action',       name: 'action',       orderable: false, searchable: false, className: 'text-center' },
        ],
        order: [[3, 'desc']],
        responsive: true,
        pageLength: 15,
        language: { searchPlaceholder: 'Search name or phone…', processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Loading…' },
    });

    // ── Filter bar wiring ────────────────────────────────────────────────────
    $('#filterPatient').on('select2:select select2:clear', function () { table.ajax.reload(); });
    $('#filterStatus').on('change', function () { table.ajax.reload(); });
    $('#filterDate').on('change', function () { table.ajax.reload(); });
    $('#resetFilters').on('click', function () {
        $('#filterPatient').val(null).trigger('change'); // clears Select2
        $('#filterStatus').val('');
        $('#filterDate').val('');
        table.search('').ajax.reload();
    });

    // ── WhatsApp send receipt ────────────────────────────────────────────────
    $(document).on('click', '.btn-wa-send', function () {
        var btn           = $(this);
        var appointmentId = btn.data('id');
        var token         = btn.data('token');

        Swal.fire({
            title: 'Send WhatsApp Receipt?',
            text: 'This will send the appointment receipt PDF to the patient via WhatsApp.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#25d366',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-whatsapp me-1"></i> Send',
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (!result.isConfirmed) return;

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: '/appointments/' + appointmentId + '/send-whatsapp-receipt',
                type: 'POST',
                data: { _token: token },
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sent!',
                            text: res.message,
                            timer: 2500,
                            showConfirmButton: false,
                        });
                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', res.message, 'error');
                        btn.prop('disabled', false).html('<i class="bi bi-whatsapp"></i>');
                    }
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON?.message ?? 'Something went wrong. Please try again.';
                    Swal.fire('Error', msg, 'error');
                    btn.prop('disabled', false).html('<i class="bi bi-whatsapp"></i>');
                },
            });
        });
    });

    // ── Payment status toggle ────────────────────────────────────────────────
    $(document).on('click', '.btn-toggle-payment', function () {
        var badge      = $(this);
        var id         = badge.data('id');
        var current    = badge.data('status');
        var newLabel   = current === 'paid' ? 'Unpaid' : 'Paid';
        var newIcon    = current === 'paid' ? '✗' : '✔';

        Swal.fire({
            title: 'Change Payment Status?',
            html: 'Mark this appointment as <strong>' + newLabel + '</strong>?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: current === 'paid' ? '#f59e0b' : '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: newIcon + ' Mark as ' + newLabel,
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url: '/appointments/' + id + '/toggle-payment',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.success) {
                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', 'Could not update status.', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Something went wrong.', 'error');
                }
            });
        });
    });

});
</script>
@endsection
