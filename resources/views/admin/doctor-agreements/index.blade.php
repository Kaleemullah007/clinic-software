@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-file-earmark-text me-2 text-theme-color"></i>Doctor Agreements</h4>
            @can('doctor-agreements.create')
            <a href="{{ route('doctor-agreements.create') }}" class="btn btn-theme btn-sm"><i class="bi bi-plus-lg me-1"></i> New Agreement</a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="doctorAgreementsTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Doctor</th>
                            <th>Clinic</th>
                            <th>Service</th>
                            <th>Type</th>
                            <th>Doctor Share</th>
                            <th>Clinic Share</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
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
    #doctorAgreementsTable thead th{background:#B1083C!important;color:#fff!important;border-color:#9a072f!important;white-space:nowrap;}
    #doctorAgreementsTable thead .sorting_asc,#doctorAgreementsTable thead .sorting_desc{background:#8e0630!important;}
</style>
<script>
$(function(){
    $('#doctorAgreementsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("doctor-agreements.index") }}',
        columns: [
            {data:'DT_RowIndex',         name:'DT_RowIndex',    orderable:false, searchable:false, width:'50px'},
            {data:'doctor_name',         name:'doctor.name'},
            {data:'clinic_name',         name:'clinic.name'},
            {data:'service_name',        name:'service.name'},
            {data:'share_type_badge',    name:'share_type',     orderable:true,  searchable:true},
            {data:'doctor_share_fmt',    name:'doctor_share',   orderable:true,  searchable:false},
            {data:'clinic_share_fmt',    name:'clinic_share',   orderable:true,  searchable:false},
            {data:'effective_from_fmt',  name:'effective_from', orderable:true,  searchable:false},
            {data:'effective_to_fmt',    name:'effective_to',   orderable:true,  searchable:false},
            {data:'status_badge',        name:'is_active',      orderable:true,  searchable:false},
            {data:'action',              name:'action',         orderable:false, searchable:false, className:'text-center'},
        ],
        order: [[1, 'asc']],
        responsive: true,
        pageLength: 15,
        language: {
            searchPlaceholder: 'Search...',
            processing: '<div class="spinner-border spinner-border-sm" style="color:#B1083C"></div> Loading…'
        },
    });
});
</script>
@endsection
