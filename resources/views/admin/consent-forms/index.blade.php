@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-file-earmark-check me-2 text-theme-color"></i>Consent Forms</h4>
            @can('consent-forms.create')
            <a href="{{ route('consent-forms.create') }}" class="btn btn-theme btn-sm"><i class="bi bi-plus-lg me-1"></i> New Form</a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="consentFormsTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Title</th>
                            <th>Signed</th>
                            <th>Sign Date</th>
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
    #consentFormsTable thead th{background:#B1083C!important;color:#fff!important;border-color:#9a072f!important;white-space:nowrap;}
    #consentFormsTable thead .sorting_asc,#consentFormsTable thead .sorting_desc{background:#8e0630!important;}
</style>
<script>
$(function(){
    $('#consentFormsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("consent-forms.index") }}',
        columns: [
            {data:'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false, width:'50px'},
            {data:'patient_name', name:'patient.name'},
            {data:'form_title', name:'form_title'},
            {data:'signed_badge', name:'signed', orderable:true, searchable:false},
            {data:'signed_at_fmt', name:'signed_at', orderable:true, searchable:false},
            {data:'action', name:'action', orderable:false, searchable:false, className:'text-center'},
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
