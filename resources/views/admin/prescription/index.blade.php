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
                            <th>Date</th>
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
</style>
<script>
$(function () {
    $('#prescriptionsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("prescription.index") }}',
        columns: [
            { data: 'DT_RowIndex',  name: 'DT_RowIndex',  orderable: false, searchable: false, width: '50px' },
            { data: 'patient_col',  name: 'patient_col',  orderable: false },
            { data: 'medicine',     name: 'medicine' },
            { data: 'dosage',       name: 'dosage' },
            { data: 'doctor_col',   name: 'doctor_col',   orderable: false, searchable: false },
            { data: 'date_col',     name: 'date_col',     orderable: false, searchable: false },
            { data: 'action',       name: 'action',       orderable: false, searchable: false, className: 'text-center' },
        ],
        responsive: true,
        pageLength: 15,
        language: { searchPlaceholder: 'Search prescriptions…', processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Loading…' },
    });
});
</script>
@endsection
