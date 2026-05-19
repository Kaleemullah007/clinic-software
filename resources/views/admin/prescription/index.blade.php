@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-capsule me-2 text-theme-color"></i>Prescriptions</h4>
        </div>
        <hr class="my-2">
    </div>

    @include('flash-message')

    {{-- Filter bar --}}
    <div class="row mx-1 mb-3 g-2 align-items-end">
        <div class="col-lg-4 col-md-6 col-12">
            <select id="filterPatient" style="width:100%">
                <option value="">All Patients</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}">
                        {{ $patient->name }}{{ $patient->prescriptions_count > 0 ? ' ('.$patient->prescriptions_count.')' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-6 col-12">
            <button id="resetFilter" class="btn btn-outline-secondary w-100">
                <i class="bi bi-x-circle me-1"></i>Reset
            </button>
        </div>
    </div>

    <div class="row mx-1">
        <div class="col-12">
            <div class="shadow-css p-3">
                <table id="prescriptionsTable" class="table table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Medicine</th>
                            <th>Dosage</th>
                            <th>Doctor</th>
                            <th>Appt. Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@include('admin.prescription.edit')
@endsection

@section('script')
<style>
    .text-theme-color{color:#B1083C;}
    .btn-outline-theme{border-color:#B1083C;color:#B1083C;}
    .btn-outline-theme:hover{background:#B1083C;color:#fff;}
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
    #prescriptionsTable thead th{background:linear-gradient(90deg,#B1083C 0%,#d13729 100%);color:#fff;border:none;}

    /* Select2 theme */
    #filterPatient + .select2-container .select2-selection--single {
        height:38px; border:1px solid #adb5bd; border-radius:6px; display:flex; align-items:center;
    }
    #filterPatient + .select2-container .select2-selection__rendered { line-height:38px; padding-left:10px; color:#212529; }
    #filterPatient + .select2-container .select2-selection__arrow    { height:36px; right:6px; }
    .select2-container--default .select2-results__option--highlighted { background-color:#B1083C !important; }
    .select2-container--default .select2-results__option--selected    { background-color:#f5c6d3 !important; color:#B1083C !important; }
    .select2-dropdown { border-color:#B1083C !important; border-radius:6px !important; }
    .select2-container--open .select2-selection--single { border-color:#B1083C !important; box-shadow:0 0 0 .2rem rgba(177,8,60,.2) !important; }
    .select2-selection__clear { color:#B1083C !important; font-size:1.1rem; font-weight:bold; margin-right:4px; }
</style>
<script>
$(window).on('load', function () {

    // ── Patient dropdown (Select2) ────────────────────────────────────────────
    $('#filterPatient').select2({ placeholder: 'All Patients', allowClear: true, width: '100%' });

    // ── DataTable ─────────────────────────────────────────────────────────────
    var table = $('#prescriptionsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route("prescription.index") }}',
            data: function (d) {
                d.filter_patient = $('#filterPatient').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex',    orderable: false, searchable: false, width: '50px' },
            { data: 'patient_col',    name: 'patient_col',    orderable: false },
            { data: 'medicine',       name: 'medicine' },
            { data: 'dosage',         name: 'dosage' },
            { data: 'doctor_col',     name: 'doctor_col',     orderable: false, searchable: false },
            { data: 'appt_date_col',  name: 'appt_date_col',  orderable: false, searchable: false },
            { data: 'action',         name: 'action',         orderable: false, searchable: false, className: 'text-center' },
        ],
        order: [],   // backend already orders by appt date desc
        responsive: true,
        pageLength: 15,
        language: { searchPlaceholder: 'Search prescriptions…', processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Loading…' },
    });

    // ── Filter wiring ─────────────────────────────────────────────────────────
    $('#filterPatient').on('select2:select select2:clear', function () { table.ajax.reload(); });
    $('#resetFilter').on('click', function () {
        $('#filterPatient').val(null).trigger('change');
        table.search('').ajax.reload();
    });

    // ── SweetAlert AJAX delete ────────────────────────────────────────────────
    $(document).on('click', '.btn-delete-prescription', function () {
        const btn   = $(this);
        const url   = btn.data('url');
        const token = btn.data('token');

        Swal.fire({
            title: 'Delete prescription?',
            text: 'This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#B1083C',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Yes, delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
        }).then((result) => {
            if (!result.isConfirmed) return;
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            $.ajax({
                url: url, method: 'POST',
                data: { _method: 'DELETE', _token: token },
                success: function () {
                    table.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Deleted', timer: 1800,
                        timerProgressBar: true, showConfirmButton: false,
                        confirmButtonColor: '#B1083C' });
                },
                error: function () {
                    btn.prop('disabled', false).html('<i class="bi bi-trash3"></i>');
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not delete. Try again.',
                        confirmButtonColor: '#B1083C' });
                }
            });
        });
    });
});
</script>
@endsection
