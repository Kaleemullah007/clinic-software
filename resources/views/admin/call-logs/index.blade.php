@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-telephone me-2 text-theme-color"></i>Call Logs</h4>
            @can('call-logs.create')
            <a href="{{ route('call-logs.create') }}" class="btn btn-theme btn-sm"><i class="bi bi-plus-lg me-1"></i> Log Call</a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="callLogsTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Appointment</th>
                            <th>Patient</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Called By</th>
                            <th>Call Time</th>
                            <th>Notes</th>
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
    #callLogsTable thead th{background:#B1083C!important;color:#fff!important;border-color:#9a072f!important;white-space:nowrap;}
    #callLogsTable thead .sorting_asc,#callLogsTable thead .sorting_desc{background:#8e0630!important;}
</style>
<script>
$(function(){
    $('#callLogsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("call-logs.index") }}',
        columns: [
            {data:'DT_RowIndex',  name:'DT_RowIndex',       orderable:false, searchable:false, width:'50px'},
            {data:'appointment_no',  name:'appointment.appointment_id'},
            {data:'patient_name',    name:'patient.name'},
            {data:'call_type_badge', name:'call_type',       orderable:true,  searchable:true},
            {data:'call_status_badge',name:'call_status',    orderable:true,  searchable:true},
            {data:'called_by_name',  name:'calledBy.name'},
            {data:'call_at_fmt',     name:'call_at',         orderable:true,  searchable:false},
            {data:'notes_short',     name:'notes',           orderable:false},
            {data:'action',          name:'action',          orderable:false, searchable:false, className:'text-center'},
        ],
        order: [[6, 'desc']],
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
