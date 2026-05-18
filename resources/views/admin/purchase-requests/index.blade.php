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
                <table id="prTable" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr>
                            <th>PR #</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prs as $pr)
                        <tr>
                            <td><a href="{{ route('purchase-requests.show',$pr) }}" class="fw-semibold text-theme-color">{{ $pr->pr_number }}</a></td>
                            <td>{{ $pr->requestedBy->name ?? '—' }}</td>
                            <td>
                                @php
                                    $colors = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','ordered'=>'info'];
                                @endphp
                                <span class="badge bg-{{ $colors[$pr->status] ?? 'secondary' }}">{{ ucfirst($pr->status) }}</span>
                            </td>
                            <td>{{ $pr->approvedBy->name ?? '—' }}</td>
                            <td>{{ $pr->created_at->format('d M Y') }}</td>
                            <td class="d-flex gap-1 flex-wrap">
                                <a href="{{ route('purchase-requests.show',$pr) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                @if($pr->status === 'pending')
                                    @can('purchase-requests.edit')
                                    <a href="{{ route('purchase-requests.edit',$pr) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    @endcan
                                    @can('purchase-requests.approve')
                                    <form action="{{ route('purchase-request.approve',$pr) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Approve</button>
                                    </form>
                                    <button class="btn btn-sm btn-danger" onclick="rejectPR({{ $pr->id }})"><i class="bi bi-x-lg"></i> Reject</button>
                                    @endcan
                                @endif
                                @can('purchase-requests.delete')
                                @if(in_array($pr->status,['pending','rejected']))
                                <form action="{{ route('purchase-requests.destroy',$pr) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this request?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
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
</style>
<script>
$(function(){ $('#prTable').DataTable({responsive:true,pageLength:25,order:[[4,'desc']]}); });
function rejectPR(id){
    $('#rejectForm').attr('action', '/purchase-requests/'+id+'/reject');
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endsection
