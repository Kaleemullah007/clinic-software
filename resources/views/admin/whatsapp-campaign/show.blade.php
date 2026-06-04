@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="row pt-3 mx-1 align-items-center mb-3">
        <div class="col-12 d-flex align-items-center gap-3">
            <a href="{{ route('whatsapp-campaign.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-megaphone me-2" style="color:#B1083C"></i>{{ $campaign->name }}
            </h4>
            <span class="ms-1">
                @switch($campaign->status)
                    @case('scheduled')  <span class="badge bg-info text-dark">Scheduled</span> @break
                    @case('running')    <span class="badge bg-warning text-dark"><span class="spinner-border spinner-border-sm me-1" style="width:.6rem;height:.6rem"></span>Running</span> @break
                    @case('completed')  <span class="badge bg-success">Completed</span> @break
                    @case('failed')     <span class="badge bg-danger">Failed</span> @break
                    @default            <span class="badge bg-secondary">{{ $campaign->status }}</span>
                @endswitch
            </span>
        </div>
        <hr class="mt-2 mb-0">
    </div>

    {{-- Stats Bar --}}
    <div class="row mx-1 g-3 mb-4">
        <div class="col-lg-3 col-md-6 col-6">
            <div class="wc-stat-card">
                <div class="wc-stat-icon" style="background:rgba(177,8,60,.1);color:#B1083C"><i class="bi bi-people"></i></div>
                <div class="wc-stat-num">{{ number_format($campaign->total_recipients) }}</div>
                <div class="wc-stat-lbl">Total Recipients</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="wc-stat-card">
                <div class="wc-stat-icon" style="background:rgba(16,185,129,.1);color:#10b981"><i class="bi bi-check2-all"></i></div>
                <div class="wc-stat-num">{{ number_format($campaign->sent_count) }}</div>
                <div class="wc-stat-lbl">Sent</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="wc-stat-card">
                <div class="wc-stat-icon" style="background:rgba(245,158,11,.1);color:#f59e0b"><i class="bi bi-hourglass-split"></i></div>
                <div class="wc-stat-num">{{ number_format($campaign->pending_count) }}</div>
                <div class="wc-stat-lbl">Pending</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="wc-stat-card">
                <div class="wc-stat-icon" style="background:rgba(239,68,68,.1);color:#ef4444"><i class="bi bi-x-circle"></i></div>
                <div class="wc-stat-num">{{ number_format($campaign->failed_count) }}</div>
                <div class="wc-stat-lbl">Failed</div>
            </div>
        </div>
    </div>

    {{-- Progress Bar --}}
    @if($campaign->total_recipients > 0)
    <div class="mx-1 mb-4">
        <div class="wc-form-card p-3">
            <div class="d-flex justify-content-between mb-1">
                <small class="fw-semibold">Delivery Progress</small>
                <small class="text-muted">{{ $campaign->progress_percent }}%</small>
            </div>
            <div class="progress" style="height:10px;border-radius:5px">
                <div class="progress-bar bg-success" style="width:{{ $campaign->sent_count / $campaign->total_recipients * 100 }}%;border-radius:5px 0 0 5px"></div>
                <div class="progress-bar bg-danger" style="width:{{ $campaign->failed_count / $campaign->total_recipients * 100 }}%"></div>
            </div>
            <div class="d-flex gap-4 mt-2" style="font-size:.78rem">
                <span class="text-success"><i class="bi bi-square-fill me-1"></i>Sent ({{ $campaign->sent_count }})</span>
                <span class="text-danger"><i class="bi bi-square-fill me-1"></i>Failed ({{ $campaign->failed_count }})</span>
                <span class="text-warning"><i class="bi bi-square-fill me-1"></i>Pending ({{ $campaign->pending_count }})</span>
            </div>
        </div>
    </div>
    @endif

    <div class="row mx-1 g-3 mb-4">
        {{-- Campaign Info --}}
        <div class="col-lg-4 col-12">
            <div class="wc-form-card h-100">
                <div class="wc-section-head"><i class="bi bi-info-circle me-2"></i>Campaign Info</div>
                <div class="p-3">
                    <table class="table table-sm table-borderless mb-0" style="font-size:.85rem">
                        <tr><td class="text-muted" style="width:130px">Template</td><td class="fw-semibold">{{ $campaign->template?->name ?? '—' }}</td></tr>
                        <tr><td class="text-muted">Message Type</td><td>
                            @php $type = $campaign->template?->message_type ?? '—'; @endphp
                            @if($type === 'text') <span class="badge bg-info text-dark">Text</span>
                            @elseif($type === 'image') <span class="badge" style="background:#8b5cf6;color:#fff">Image</span>
                            @elseif($type === 'both') <span class="badge" style="background:#B1083C;color:#fff">Text + Image</span>
                            @else <span class="text-muted">—</span>
                            @endif
                        </td></tr>
                        <tr><td class="text-muted">Target Role</td><td><span class="badge bg-light text-dark border">{{ ucfirst($campaign->target_role) }}</span></td></tr>
                        @if($campaign->clinic)<tr><td class="text-muted">Clinic</td><td>{{ $campaign->clinic->name }}</td></tr>@endif
                        @if($campaign->doctor)<tr><td class="text-muted">Doctor</td><td>{{ $campaign->doctor->name }}</td></tr>@endif
                        <tr><td class="text-muted">Scheduled</td><td>{{ $campaign->scheduled_at->format('d M Y, H:i') }}</td></tr>
                        <tr><td class="text-muted">Timezone</td><td>{{ $campaign->timezone }}</td></tr>
                        <tr><td class="text-muted">Msg Delay</td><td>{{ $campaign->message_delay }}s</td></tr>
                        <tr><td class="text-muted">Created By</td><td>{{ $campaign->creator?->name ?? '—' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Message Preview --}}
        <div class="col-lg-8 col-12">
            <div class="wc-form-card h-100">
                <div class="wc-section-head"><i class="bi bi-chat-dots me-2"></i>Message Preview</div>
                <div class="p-3">
                    <div class="wc-whatsapp-preview">
                        @if($campaign->template?->image_path)
                            <img src="{{ $campaign->template->image_url }}" alt="Campaign Image"
                                class="img-fluid rounded mb-2" style="max-height:200px;width:100%;object-fit:cover">
                        @endif
                        @if($campaign->template?->message_body)
                            <p class="mb-0" style="font-size:.9rem;white-space:pre-line">{{ $campaign->template->message_body }}</p>
                        @endif
                        @if(!$campaign->template?->image_path && !$campaign->template?->message_body)
                            <span class="text-muted small">No preview available.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recipients DataTable --}}
    <div class="mx-1 shadow-css p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0"><i class="bi bi-list-check me-2" style="color:#B1083C"></i>Send Logs</h6>
            <div class="d-flex gap-2">
                <select id="filterStatus" class="form-select form-select-sm border-secondary" style="width:140px">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="sent">Sent</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
        </div>
        <table id="logsTable" class="table table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Recipient</th>
                    <th class="text-center">Status</th>
                    <th>Sent At</th>
                    <th>Message ID</th>
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
    var table = $('#logsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route("whatsapp-campaign.show", $campaign->id) }}',
            data: function (d) { d.filter_status = $('#filterStatus').val(); }
        },
        columns: [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex',  orderable: false, searchable: false, width: '50px' },
            { data: 'recipient_col',  name: 'recipient_name' },
            { data: 'status_col',     name: 'status',        orderable: false, className: 'text-center' },
            { data: 'sent_at_col',    name: 'sent_at' },
            { data: 'meta_message_id',name: 'meta_message_id', render: d => d ? '<small class="text-muted">' + d + '</small>' : '—' },
        ],
        order: [[3, 'desc']],
        responsive: true,
        pageLength: 25,
        language: { searchPlaceholder: 'Search recipients…', processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Loading…' },
    });

    $('#filterStatus').on('change', function () { table.ajax.reload(); });

    @if($campaign->status === 'running')
    // Auto-refresh every 10s while running
    setInterval(function () {
        table.ajax.reload(null, false);
        location.reload();
    }, 10000);
    @endif
});
</script>
@endsection
