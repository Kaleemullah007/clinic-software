@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="row pt-3 mx-1 align-items-center mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-megaphone me-2" style="color:#B1083C"></i>WhatsApp Campaigns
            </h4>
            <div class="d-flex gap-2">
                @can('whatsapp-campaign.templates')
                <a href="{{ route('whatsapp-campaign.templates') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-file-earmark-text me-1"></i>Templates
                </a>
                @endcan
                @can('whatsapp-campaign.create')
                <a href="{{ route('whatsapp-campaign.create') }}" class="btn btn-sm btn-danger">
                    <i class="bi bi-plus-circle me-1"></i>New Campaign
                </a>
                @endcan
            </div>
        </div>
        <hr class="mt-2 mb-0">
    </div>

    {{-- Summary Cards --}}
    <div class="row mx-1 g-3 mb-4">
        <div class="col-lg-3 col-md-6 col-6">
            <div class="wc-stat-card">
                <div class="wc-stat-icon" style="background:rgba(177,8,60,.1);color:#B1083C"><i class="bi bi-megaphone"></i></div>
                <div class="wc-stat-num">{{ number_format($totalCampaigns) }}</div>
                <div class="wc-stat-lbl">Total Campaigns</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="wc-stat-card">
                <div class="wc-stat-icon" style="background:rgba(245,158,11,.1);color:#f59e0b"><i class="bi bi-send-check"></i></div>
                <div class="wc-stat-num">{{ number_format($running) }}</div>
                <div class="wc-stat-lbl">Running Now</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="wc-stat-card">
                <div class="wc-stat-icon" style="background:rgba(16,185,129,.1);color:#10b981"><i class="bi bi-check2-all"></i></div>
                <div class="wc-stat-num">{{ number_format($completed) }}</div>
                <div class="wc-stat-lbl">Completed</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="wc-stat-card">
                <div class="wc-stat-icon" style="background:rgba(37,211,102,.1);color:#25d366"><i class="bi bi-whatsapp"></i></div>
                <div class="wc-stat-num">{{ number_format($totalSent) }}</div>
                <div class="wc-stat-lbl">Messages Sent</div>
            </div>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="mx-1 shadow-css p-3">
        @include('flash-message')
        <table id="campaignsTable" class="table table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Campaign</th>
                    <th>Target</th>
                    <th>Scheduled</th>
                    <th>Status</th>
                    <th>Sent / Pending / Failed</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
        </table>
    </div>

</div>
@endsection

@section('script')
@include('admin.whatsapp-campaign._styles')
<script>
$(window).on('load', function () {
    var table = $('#campaignsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("whatsapp-campaign.index") }}',
        columns: [
            { data: 'DT_RowIndex',   name: 'DT_RowIndex',  orderable: false, searchable: false, width: '50px' },
            { data: 'name_col',      name: 'name',         orderable: true },
            { data: 'target_col',    name: 'target_role',  orderable: false },
            { data: 'scheduled_col', name: 'scheduled_at', orderable: true },
            { data: 'status_col',    name: 'status',       orderable: false, width: '110px' },
            { data: 'stats_col',     name: 'sent_count',   orderable: false, width: '180px' },
            { data: 'action',        name: 'action',       orderable: false, searchable: false, className: 'text-center' },
        ],
        order: [[3, 'desc']],
        responsive: true,
        pageLength: 15,
        language: { searchPlaceholder: 'Search campaigns…', processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Loading…' },
    });

    // Delete campaign
    $(document).on('click', '.btn-delete-campaign', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Delete Campaign?',
            text: 'This will also delete all send logs for this campaign.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#B1083C',
            confirmButtonText: 'Yes, Delete',
        }).then(function (r) {
            if (!r.isConfirmed) return;
            $.ajax({
                url: '{{ url("whatsapp-campaign") }}/' + id,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function () { table.ajax.reload(); }
            });
        });
    });
});
</script>
@endsection
