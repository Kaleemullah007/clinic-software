@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">Clinics</h4>
            @can('create', \App\Models\Clinic::class)
            <a href="{{ route('clinic.create') }}" class="btn btn-sm btn-success"><i class="bi bi-plus-lg me-1"></i> Add Clinic</a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="clinicTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Support Email</th>
                            <th>Notification Email</th>
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
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
    #clinicTable thead th{background:#B1083C!important;color:#fff!important;border-color:#9a072f!important;white-space:nowrap;}
    #clinicTable thead .sorting_asc,#clinicTable thead .sorting_desc{background:#8e0630!important;}
</style>
<script>
$(function(){
    $('#clinicTable').DataTable({
        serverSide:true, processing:true,
        ajax:'{{ route("clinic.index") }}',
        columns:[
            {data:'DT_RowIndex',name:'DT_RowIndex',orderable:false,searchable:false,width:'50px'},
            {data:'name',name:'name'},
            {data:'phone',name:'phone'},
            {data:'address',name:'address'},
            {data:'support_email',name:'support_email'},
            {data:'notification_email',name:'notification_email'},
            {data:'status',name:'status',orderable:false,searchable:false,className:'text-center'},
            {data:'action',name:'action',orderable:false,searchable:false,className:'text-center'},
        ],
        order:[[1,'asc']],
        responsive:true,
        pageLength:15,
        language:{searchPlaceholder:'Search...',processing:'<div class="spinner-border spinner-border-sm" style="color:#B1083C"></div> Loading…'},
    });
});
</script>
@endsection
