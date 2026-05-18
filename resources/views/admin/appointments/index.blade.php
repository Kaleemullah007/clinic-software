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
                <select class="form-select border-secondary" id="filterPatient">
                    <option value="">All Patients</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->name }}</option>
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
</style>
<script>
$(function () {
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

    // Wire filter bar → reload DataTable
    $('#filterPatient, #filterStatus').on('change', function () { table.ajax.reload(); });
    $('#filterDate').on('change', function () { table.ajax.reload(); });
    $('#resetFilters').on('click', function () {
        $('#filterPatient, #filterStatus').val('');
        $('#filterDate').val('');
        table.search('').ajax.reload();
    });

    // Prescription modal
    document.addEventListener('show.bs.modal', function (e) {
        if (e.target.id !== 'prescriptionModal') return;
        var btn = e.relatedTarget;
        if (!btn) return;
        document.getElementById('px_appointment_id').value = btn.dataset.appointmentId || '';
        document.getElementById('px_user_id').value        = btn.dataset.userId || '';
    });
});
</script>
@endsection
