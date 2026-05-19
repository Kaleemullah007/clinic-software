@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-cart-plus me-2 text-theme-color"></i>Purchase Requests</h4>
            @can('purchase-requests.create')
            <a href="{{ route('purchase-requests.create') }}" class="btn btn-theme btn-sm">
                <i class="bi bi-plus-lg me-1"></i> New Request
            </a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="purchaseRequestsTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>PR #</th>
                            <th>Items</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header"><h6 class="modal-title fw-bold">Reject Request</h6></div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <textarea name="reject_reason" class="form-control border-secondary" rows="3" placeholder="Reason (optional)"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<style>
    .text-theme-color{color:#B1083C;}
    .btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
    #purchaseRequestsTable thead th{background:#B1083C!important;color:#fff!important;border-color:#9a072f!important;white-space:nowrap;}
    #purchaseRequestsTable thead .sorting_asc,#purchaseRequestsTable thead .sorting_desc{background:#8e0630!important;}
</style>
<script>
$(function(){
    $('#purchaseRequestsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("purchase-requests.index") }}',
        columns: [
            {data:'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false, width:'50px'},
            {data:'pr_number_link', name:'pr_number', className:'fw-semibold'},
            {data:'items_count', name:'items_count', orderable:false, searchable:false, className:'text-center'},
            {data:'requested_by_name', name:'requestedBy.name'},
            {data:'status_badge', name:'status', orderable:true, searchable:true},
            {data:'approved_by_name', name:'approvedBy.name'},
            {data:'date', name:'created_at'},
            {data:'action', name:'action', orderable:false, searchable:false, className:'text-center'},
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

function rejectPR(id){
    $('#rejectForm').attr('action', '/purchase-requests/' + id + '/reject');
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endsection
