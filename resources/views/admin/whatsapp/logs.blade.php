@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="container">

        {{-- ── Header ──────────────────────────────────────────────────────── --}}
        <div class="row pt-3 align-items-center mb-3">
            <div class="col">
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-whatsapp me-2" style="color:#25d366;"></i>WhatsApp Logs
                </h4>
                <small class="text-muted">Track all WhatsApp receipt messages sent to patients</small>
            </div>
        </div>

        {{-- ── Summary stat cards ──────────────────────────────────────────── --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background:linear-gradient(135deg,#25d366,#1ebe5d);flex-shrink:0;">
                            <i class="bi bi-check-circle text-white fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 lh-1">{{ number_format($totalSent) }}</div>
                            <div class="text-muted small">Total Sent</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background:linear-gradient(135deg,#dc3545,#f28b82);flex-shrink:0;">
                            <i class="bi bi-x-circle text-white fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 lh-1">{{ number_format($totalFailed) }}</div>
                            <div class="text-muted small">Total Failed</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background:linear-gradient(135deg,#0d6efd,#6ea8fe);flex-shrink:0;">
                            <i class="bi bi-calendar-day text-white fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 lh-1">{{ number_format($sentToday) }}</div>
                            <div class="text-muted small">Sent Today</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background:linear-gradient(135deg,#6f42c1,#a37fe0);flex-shrink:0;">
                            <i class="bi bi-calendar-month text-white fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 lh-1">{{ number_format($sentThisMonth) }}</div>
                            <div class="text-muted small">This Month</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Filter bar ───────────────────────────────────────────────────── --}}
        <div class="card border-0 shadow-sm mb-3 px-3 py-3">
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small text-muted mb-1">From</label>
                    <input type="date" id="filterFrom" class="form-control form-control-sm border-secondary" style="max-width:145px;">
                </div>
                <div class="col-auto">
                    <label class="form-label small text-muted mb-1">To</label>
                    <input type="date" id="filterTo" class="form-control form-control-sm border-secondary" style="max-width:145px;">
                </div>
                <div class="col-auto">
                    <label class="form-label small text-muted mb-1">Status</label>
                    <select id="filterStatus" class="form-select form-select-sm border-secondary" style="min-width:130px;">
                        <option value="">All Statuses</option>
                        <option value="sent">Sent</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button id="applyFilters" class="btn btn-sm text-white px-4"
                            style="background:linear-gradient(90deg,#25d366,#1ebe5d);border:none;">
                        <i class="bi bi-funnel me-1"></i>Apply
                    </button>
                    <button id="resetFilters" class="btn btn-sm btn-outline-secondary ms-1 px-3">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- ── DataTable ────────────────────────────────────────────────────── --}}
        <div class="shadow-css p-3">
            <table id="whatsappLogsTable" class="table table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date &amp; Time</th>
                        <th>Patient</th>
                        <th>Appointment</th>
                        <th>Sent By</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>
</div>
@endsection

@section('script')
<style>
    .shadow-css { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.08); }
    #whatsappLogsTable thead th {
        background: linear-gradient(90deg,#25d366 0%,#1ebe5d 100%);
        color: #fff;
        border: none;
    }
</style>
<script>
$(window).on('load', function () {

    var logsTable = $('#whatsappLogsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route("whatsapp.logs") }}',
            data: function (d) {
                d.date_from     = $('#filterFrom').val();
                d.date_to       = $('#filterTo').val();
                d.filter_status = $('#filterStatus').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex',  orderable: false, searchable: false, width: '50px' },
            { data: 'sent_at_fmt',    name: 'created_at',   searchable: false },
            { data: 'patient_col',    name: 'patient_col',  orderable: false },
            { data: 'appointment_col',name: 'appointment_col', orderable: false, searchable: false },
            { data: 'sender_col',     name: 'sender_col',   orderable: false, searchable: false },
            { data: 'status_col',     name: 'status_col',   orderable: false, searchable: false },
        ],
        order: [[1, 'desc']],
        responsive: true,
        pageLength: 20,
        language: {
            searchPlaceholder: 'Search patient or phone…',
            processing: '<div class="spinner-border spinner-border-sm text-success"></div> Loading…'
        },
    });

    $('#applyFilters').on('click', function () { logsTable.ajax.reload(); });
    $('#resetFilters').on('click', function () {
        $('#filterFrom').val('');
        $('#filterTo').val('');
        $('#filterStatus').val('');
        logsTable.search('').ajax.reload();
    });

});
</script>
@endsection
