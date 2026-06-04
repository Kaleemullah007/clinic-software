@extends('layouts.admin')
@section('title', 'Device Approvals')

@section('content')
<style>
    .da-tab-btn {
        border: 2px solid #e2e8f0; border-radius: 8px; padding: 8px 18px;
        font-weight: 600; font-size: .85rem; cursor: pointer; background: #fff;
        display: inline-flex; align-items: center; gap: 7px;
        transition: border-color .15s, background .15s;
    }
    .da-tab-btn.active       { border-color: #B1083C; background: #fff5f7; color: #B1083C; }
    .da-tab-btn:hover:not(.active) { border-color: #cbd5e1; background: #f8fafc; }
    .da-count { border-radius: 20px; padding: 1px 8px; font-size: .72rem; font-weight: 700; }
    .cnt-all      { background: #e2e8f0; color: #374151; }
    .cnt-pending  { background: #fef9c3; color: #854d0e; }
    .cnt-approved { background: #dcfce7; color: #166534; }
    .cnt-rejected { background: #fee2e2; color: #991b1b; }
    .toggle-track {
        width: 46px; height: 24px; border-radius: 12px; background: #e2e8f0;
        position: relative; cursor: pointer; transition: background .2s;
        display: inline-block; flex-shrink: 0;
    }
    .toggle-track.on  { background: #B1083C; }
    .toggle-thumb {
        position: absolute; top: 3px; left: 3px; width: 18px; height: 18px;
        border-radius: 50%; background: #fff; transition: left .2s;
        box-shadow: 0 1px 3px rgba(0,0,0,.2);
    }
    .toggle-track.on .toggle-thumb { left: 25px; }
</style>

<div class="container-fluid">

    {{-- ── Header ──────────────────────────────────────────────────────── --}}
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-shield-lock me-2" style="color:#B1083C"></i>Device Approvals
            </h4>

            {{-- Global toggle --}}
            <div class="d-flex align-items-center gap-3 shadow-sm p-2 px-3 rounded bg-white">
                <div>
                    <div class="fw-semibold small">Device Approval Feature</div>
                    <div class="text-muted" style="font-size:.75rem" id="toggleLabel">
                        {{ $deviceApprovalEnabled == '1' ? 'Enabled — new browsers require approval' : 'Disabled — all users login freely' }}
                    </div>
                </div>
                <div class="toggle-track {{ $deviceApprovalEnabled == '1' ? 'on' : '' }}" id="featureToggle" title="Toggle device approval">
                    <div class="toggle-thumb"></div>
                </div>
            </div>
        </div>
        <hr class="my-3">
    </div>

    {{-- ── Tabs ─────────────────────────────────────────────────────────── --}}
    <div class="row mx-1 mb-3">
        <div class="col-12 d-flex flex-wrap gap-2">
            <button class="da-tab-btn active" data-status="all">
                <i class="bi bi-grid"></i> All
                <span class="da-count cnt-all" id="cnt-all">{{ $counts['all'] }}</span>
            </button>
            <button class="da-tab-btn" data-status="pending">
                <i class="bi bi-hourglass-split"></i> Pending
                <span class="da-count cnt-pending" id="cnt-pending">{{ $counts['pending'] }}</span>
            </button>
            <button class="da-tab-btn" data-status="approved">
                <i class="bi bi-check-circle"></i> Approved
                <span class="da-count cnt-approved" id="cnt-approved">{{ $counts['approved'] }}</span>
            </button>
            <button class="da-tab-btn" data-status="rejected">
                <i class="bi bi-x-circle"></i> Rejected
                <span class="da-count cnt-rejected" id="cnt-rejected">{{ $counts['rejected'] }}</span>
            </button>
        </div>
    </div>

    {{-- ── DataTable ───────────────────────────────────────────────────── --}}
    <div class="row mx-1">
        <div class="col-12">
            <div class="shadow-css p-3">
                <table id="deviceTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>User</th>
                            <th>Device / Browser</th>
                            <th>Requested At</th>
                            <th>Status</th>
                            <th>Actioned By</th>
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
    .shadow-css { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.07); }
</style>
<script>
var CSRF = document.querySelector('meta[name="csrf-token"]').content;
var currentStatus = 'all';
var table;

// ── SweetAlert2 toast helper (replaces toastr) ────────────────────────────
function toast(icon, message) {
    Swal.fire({
        icon: icon,
        title: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
}

// ── DataTable — wait for defer scripts (datatables.min.js) to finish ─────
window.addEventListener('load', function () {

    table = $('#deviceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("device-approvals.data") }}',
            data: function (d) { d.status = currentStatus; }
        },
        columns: [
            { data: 'DT_RowIndex',  name: 'id',          orderable: false, searchable: false, width: '40px' },
            { data: 'code_col',     name: 'code',         orderable: false },
            { data: 'user_col',     name: 'u_name',       orderable: false },
            { data: 'device_col',   name: 'browser',      orderable: false },
            { data: 'created_at',   name: 'created_at',
              render: function (d) {
                  return d ? new Date(d).toLocaleString('en-GB', {
                      day:'2-digit', month:'short', year:'numeric',
                      hour:'2-digit', minute:'2-digit'
                  }) : '—';
              }
            },
            { data: 'status_col',   name: 'status',       orderable: true  },
            { data: 'actioned_col', name: 'actioned_at',  orderable: false },
            { data: 'actions',      name: 'actions',      orderable: false, searchable: false },
        ],
        order: [[4, 'desc']],
        pageLength: 15,
        language: {
            search: 'Search:',
            searchPlaceholder: 'Code, user, IP…',
            emptyTable: 'No device requests found.',
            processing: '<div class="spinner-border spinner-border-sm text-danger me-2"></div> Loading…',
        },
        drawCallback: function () { updateCounts(); }
    });

}); // end window.load

// ── Tab switching ─────────────────────────────────────────────────────────
document.querySelectorAll('.da-tab-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.da-tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentStatus = this.dataset.status;
        if (table) table.ajax.reload();
    });
});

// ── Approve / Reject / Revoke (event delegation) ──────────────────────────
$('#deviceTable').on('click', '.btn-approve', function () {
    var id = $(this).data('id');
    Swal.fire({ title:'Approve this device?', icon:'question', showCancelButton:true,
        confirmButtonColor:'#10b981', cancelButtonColor:'#6c757d',
        confirmButtonText:'<i class="bi bi-check-lg me-1"></i>Approve' })
    .then(r => { if (r.isConfirmed) doAction('{{ url("device-approvals") }}/' + id + '/approve'); });
});

$('#deviceTable').on('click', '.btn-reject', function () {
    var id = $(this).data('id');
    Swal.fire({ title:'Reject this device?', icon:'warning', showCancelButton:true,
        confirmButtonColor:'#ef4444', cancelButtonColor:'#6c757d',
        confirmButtonText:'<i class="bi bi-x-lg me-1"></i>Reject' })
    .then(r => { if (r.isConfirmed) doAction('{{ url("device-approvals") }}/' + id + '/reject'); });
});

$('#deviceTable').on('click', '.btn-revoke', function () {
    var id = $(this).data('id');
    Swal.fire({ title:'Revoke access?', text:'The user will need approval again from this browser.',
        icon:'warning', showCancelButton:true,
        confirmButtonColor:'#f59e0b', cancelButtonColor:'#6c757d',
        confirmButtonText:'<i class="bi bi-slash-circle me-1"></i>Revoke' })
    .then(r => { if (r.isConfirmed) doAction('{{ url("device-approvals") }}/' + id + '/revoke'); });
});

function doAction(url) {
    fetch(url, { method:'POST', headers:{ 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' } })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            toast('success', data.message);
            if (table) table.ajax.reload(null, false);
            updateCounts();
        } else {
            toast('error', 'Action failed.');
        }
    })
    .catch(() => toast('error', 'Request failed.'));
}

// ── Update tab counts ─────────────────────────────────────────────────────
function updateCounts() {
    fetch('{{ route("device-approvals.data") }}?status=all&draw=1&start=0&length=1', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        // Recount from fresh queries
        ['all','pending','approved','rejected'].forEach(function(s) {
            var el = document.getElementById('cnt-' + s);
            if (el && d['cnt_' + s] !== undefined) el.textContent = d['cnt_' + s];
        });
    }).catch(() => {});
}

// ── Feature toggle ────────────────────────────────────────────────────────
var toggleEl = document.getElementById('featureToggle');
var isEnabled = {{ $deviceApprovalEnabled == '1' ? 'true' : 'false' }};

toggleEl.addEventListener('click', function () {
    isEnabled = !isEnabled;
    this.classList.toggle('on', isEnabled);

    fetch('{{ route("device-approvals.toggle-setting") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ enabled: isEnabled })
    })
    .then(r => r.json())
    .then(data => {
        toast('success', data.message);
        document.getElementById('toggleLabel').textContent = isEnabled
            ? 'Enabled — new browsers require approval'
            : 'Disabled — all users login freely';
    })
    .catch(() => {
        isEnabled = !isEnabled;
        toggleEl.classList.toggle('on', isEnabled);
        toast('error', 'Failed to update setting.');
    });
});
</script>
@endsection
