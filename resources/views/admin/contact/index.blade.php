@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">Contacts</h4>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="contactTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Message</th>
                            <th>Created At</th>
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
    #contactTable thead th{background:#B1083C!important;color:#fff!important;border-color:#9a072f!important;white-space:nowrap;}
    #contactTable thead .sorting_asc,#contactTable thead .sorting_desc{background:#8e0630!important;}
</style>
<script>
$(function(){
    $('#contactTable').DataTable({
        serverSide:true, processing:true,
        ajax:'{{ route("contacts.index") }}',
        columns:[
            {data:'DT_RowIndex',name:'DT_RowIndex',orderable:false,searchable:false,width:'50px'},
            {data:'name',name:'name'},
            {data:'email',name:'email'},
            {data:'phone',name:'phone'},
            {data:'message_short',name:'message',searchable:true,orderable:false},
            {data:'created_at',name:'created_at'},
            {data:'action',name:'action',orderable:false,searchable:false,className:'text-center'},
        ],
        order:[[5,'desc']],
        responsive:true,
        pageLength:15,
        language:{searchPlaceholder:'Search...',processing:'<div class="spinner-border spinner-border-sm" style="color:#B1083C"></div> Loading…'},
    });
});
</script>
@endsection
