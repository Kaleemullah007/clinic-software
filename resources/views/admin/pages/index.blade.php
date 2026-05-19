@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">Pages</h4>
            @can('create', \App\Models\Page::class)
            <a href="{{ route('pages.create') }}" class="btn btn-sm btn-success"><i class="bi bi-plus-lg me-1"></i> Add Page</a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="pageTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Slug</th>
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
    #pageTable thead th{background:#B1083C!important;color:#fff!important;border-color:#9a072f!important;white-space:nowrap;}
    #pageTable thead .sorting_asc,#pageTable thead .sorting_desc{background:#8e0630!important;}
</style>
<script>
$(function(){
    $('#pageTable').DataTable({
        serverSide:true, processing:true,
        ajax:'{{ route("pages.index") }}',
        columns:[
            {data:'DT_RowIndex',name:'DT_RowIndex',orderable:false,searchable:false,width:'50px'},
            {data:'title',name:'title'},
            {data:'slug',name:'slug'},
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
