@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-cart-check me-2 text-theme-color"></i>{{ $purchaseRequest->pr_number }}</h4>
            <a href="{{ route('purchase-requests.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2">
        <div class="col-lg-8 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Status:</strong>
                        @php $colors=['pending'=>'warning','approved'=>'success','rejected'=>'danger','ordered'=>'info']; @endphp
                        <span class="badge bg-{{ $colors[$purchaseRequest->status]??'secondary' }} ms-1">{{ ucfirst($purchaseRequest->status) }}</span>
                    </div>
                    <div class="col-md-4"><strong>Requested By:</strong> {{ $purchaseRequest->requestedBy->name }}</div>
                    <div class="col-md-4"><strong>Date:</strong> {{ $purchaseRequest->created_at->format('d M Y') }}</div>
                </div>
                @if($purchaseRequest->approvedBy)
                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ ucfirst($purchaseRequest->status) }} By:</strong> {{ $purchaseRequest->approvedBy->name }}</div>
                    <div class="col-md-4"><strong>At:</strong> {{ $purchaseRequest->approved_at?->format('d M Y H:i') }}</div>
                </div>
                @endif
                @if($purchaseRequest->notes)
                <div class="alert alert-light border mb-3"><strong>Notes:</strong><br>{{ $purchaseRequest->notes }}</div>
                @endif

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr><th>#</th><th>Product</th><th>Variation</th><th>Qty</th><th>Notes</th></tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseRequest->items as $i => $item)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $item->product->name ?? '—' }}</td>
                            <td>{{ $item->variation->name ?? '—' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($purchaseRequest->status === 'pending')
                    @can('purchase-requests.approve')
                    <div class="d-flex gap-2 mt-3">
                        <form action="{{ route('purchase-request.approve', $purchaseRequest) }}" method="POST">
                            @csrf
                            <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Approve</button>
                        </form>
                        <button class="btn btn-danger" onclick="rejectPR({{ $purchaseRequest->id }})">
                            <i class="bi bi-x-lg me-1"></i> Reject
                        </button>
                    </div>
                    @endcan
                @endif
            </div>
        </div>
    </div>
</div>

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
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
</style>
<script>
function rejectPR(id){
    $('#rejectForm').attr('action', '/purchase-requests/'+id+'/reject');
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endsection
