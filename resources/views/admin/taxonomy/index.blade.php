@extends('layouts.admin')

@section('content')

<style>
    :root { --brand: #B1083C; --brand-light: #f8e5ec; --brand-hover: #8e0630; }

    /* ── Cards ── */
    .taxonomy-card { border: none; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
    .taxonomy-card .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: .85rem 1.2rem;
        font-weight: 600;
        font-size: .95rem;
    }
    .card-header-brand { background: var(--brand); color: #fff; }
    .card-header-light { background: #f8f9fa; color: #333; border-bottom: 1px solid #dee2e6; }

    /* ── Panel list — thin custom scrollbar, no layout-shift on hover ── */
    .panel-list {
        min-height: 320px;
        max-height: 420px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #fafafa;
        scrollbar-width: thin;                  /* Firefox */
        scrollbar-color: #ddd transparent;      /* Firefox */
    }
    .panel-list::-webkit-scrollbar          { width: 5px; }
    .panel-list::-webkit-scrollbar-track    { background: transparent; }
    .panel-list::-webkit-scrollbar-thumb    { background: #ddd; border-radius: 3px; }
    .panel-list::-webkit-scrollbar-thumb:hover { background: var(--brand); }

    .panel-list .empty-state {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; height: 300px; color: #aaa;
    }

    /* ── Items ── */
    .panel-item {
        display: flex; align-items: flex-start; padding: .6rem .8rem;
        border-bottom: 1px solid #f0f0f0; background: #fff; transition: background .15s;
    }
    .panel-item:last-child { border-bottom: none; }
    .panel-item:hover      { background: var(--brand-light); }
    .panel-item .item-meta { font-size: .78rem; color: #6c757d; margin-top: 2px; }
    .panel-item .item-name { font-weight: 600; font-size: .88rem; color: #212529; }

    /* ── Buttons ── */
    .btn-brand          { background: var(--brand); border-color: var(--brand); color: #fff; }
    .btn-brand:hover    { background: var(--brand-hover); border-color: var(--brand-hover); color: #fff; }
    .btn-brand:disabled { background: #ccc; border-color: #ccc; color: #fff; cursor: not-allowed; }

    .badge-count {
        background: #fff; color: var(--brand); font-size: .75rem;
        padding: 2px 8px; border-radius: 20px; font-weight: 700;
    }

    /* ── Progress bar ── */
    .progress-wrap    { height: 22px; border-radius: 8px; overflow: hidden; background: #e9ecef; }
    .progress-bar-brand {
        background: var(--brand); height: 100%; transition: width .3s ease;
        display: flex; align-items: center; justify-content: center;
        font-size: .78rem; font-weight: 600; color: #fff;
    }

    /* ── Misc ── */
    .source-checkbox { accent-color: var(--brand); width: 16px; height: 16px; cursor: pointer; flex-shrink: 0; }
    .remove-staged   { width: 26px; height: 26px; padding: 0; line-height: 1; flex-shrink: 0; border-radius: 50% !important; }
    .section-divider { border-left: 3px solid var(--brand); padding-left: .75rem; margin-bottom: 1rem; }

    /* ── Responsive — mobile: remove equal-height, reduce list min-height ── */
    @media (max-width: 991.98px) {
        .taxonomy-card.h-100 { height: auto !important; }
        .panel-list          { min-height: 220px; max-height: 300px; }
        .panel-item .item-meta span { display: block; margin-left: 0 !important; }
    }
    @media (max-width: 575.98px) {
        .panel-list          { min-height: 180px; max-height: 260px; }
        .taxonomy-card .card-header { font-size: .85rem; }
    }
</style>

<div class="container-fluid py-3" style="overflow-x:hidden">

    {{-- ── Page Header ── --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="section-divider">
                <h4 class="mb-0 fw-bold">Service Taxonomy Migration</h4>
                <small class="text-muted">Reassign appointment services from one category to another in bulk.</small>
            </div>
        </div>
    </div>

    {{-- ── Success / Error Alerts ── --}}
    <div id="successAlert" class="alert border-0 d-none mb-3" role="alert"
         style="background:var(--brand-light); color: var(--brand); border-left: 4px solid var(--brand) !important;">
        <i class="bi bi-check-circle-fill me-2"></i>
        <span id="successMessage"></span>
    </div>
    <div id="errorAlert" class="alert alert-danger border-0 d-none mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <span id="errorMessage"></span>
    </div>

    {{-- ── Progress Section (hidden until migration starts) ── --}}
    <div id="progressSection" class="d-none mb-3">
        <div class="card taxonomy-card">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold" id="progressLabel">
                        <span class="spinner-border spinner-border-sm me-2" style="color:var(--brand)"></span>
                        Migration in progress…
                    </span>
                    <span class="text-muted small" id="progressText">0 / 0</span>
                </div>
                <div class="progress-wrap">
                    <div class="progress-bar-brand" id="progressBar" style="width:0%">0%</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Two-Column Layout ── --}}
    <div class="row g-3">

        {{-- ════════════════════════════ LEFT — SOURCE PANEL ════════════════════════════ --}}
        <div class="col-lg-6">
            <div class="card taxonomy-card h-100">
                <div class="card-header card-header-light d-flex align-items-center">
                    <i class="bi bi-grid-3x3-gap me-2 fs-5" style="color:var(--brand)"></i>
                    <span>Source Service — Select Items to Migrate</span>
                </div>
                <div class="card-body d-flex flex-column gap-3">

                    {{-- Source Service Dropdown --}}
                    <div>
                        <label class="form-label fw-semibold mb-1">Select Source Service</label>
                        <select id="sourceService" class="form-select border-secondary">
                            <option value="">— Select a service —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Select All / None toolbar --}}
                    <div class="d-flex align-items-center gap-2 d-none" id="selectionToolbar">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAllBtn">
                            <i class="bi bi-check2-all me-1"></i>Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="selectNoneBtn">
                            <i class="bi bi-x-square me-1"></i>Select None
                        </button>
                        <span class="ms-auto text-muted small" id="rightCount">0 items</span>
                    </div>

                    {{-- Source List --}}
                    <div class="panel-list flex-grow-1" id="sourceList">
                        <div class="empty-state" id="sourceEmpty">
                            <i class="bi bi-arrow-down-circle fs-1 mb-2" style="color:var(--brand); opacity:.4"></i>
                            <span>Select a source service above to load items.</span>
                        </div>
                    </div>

                    {{-- Move to Staging Button --}}
                    <div>
                        <button type="button" class="btn btn-brand" id="moveToStagingBtn" disabled>
                            <i class="bi bi-arrow-right me-1"></i>Move to Staging
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- ════════════════════════════ RIGHT — STAGING AREA ════════════════════════════ --}}
        <div class="col-lg-6">
            <div class="card taxonomy-card h-100">
                <div class="card-header card-header-brand d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-check2-square me-2"></i>Staging Area — To Be Migrated</span>
                    <span class="badge-count" id="stagingCount">0</span>
                </div>
                <div class="card-body d-flex flex-column gap-3">

                    {{-- Target Service --}}
                    <div>
                        <label class="form-label fw-semibold mb-1">
                            Target Service <span class="text-danger">*</span>
                        </label>
                        <select id="targetService" class="form-select border-secondary">
                            <option value="">— Select target service —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <div class="text-danger small mt-1 d-none" id="targetError">
                            <i class="bi bi-exclamation-circle me-1"></i>Please select a target service before proceeding.
                        </div>
                    </div>

                    {{-- Staged List --}}
                    <div class="panel-list flex-grow-1" id="stagingList">
                        <div class="empty-state" id="stagingEmpty">
                            <i class="bi bi-arrow-right-circle fs-1 mb-2" style="color:var(--brand); opacity:.4"></i>
                            <span>Move items from the left panel to stage them here.</span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary" id="previewBtn" disabled>
                            <i class="bi bi-eye me-1"></i>Preview
                        </button>
                        <button type="button" class="btn btn-brand fw-semibold" id="migrateBtn" disabled>
                            <i class="bi bi-arrow-repeat me-1"></i>Move Service
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>{{-- /row --}}
</div>

{{-- ── Preview Modal ── --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background:var(--brand)">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="bi bi-eye me-2"></i>Migration Preview
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewBody">
                <div class="text-center py-4">
                    <div class="spinner-border" style="color:var(--brand)"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-brand fw-semibold" id="confirmMigrateBtn">
                    <i class="bi bi-arrow-repeat me-1"></i>Confirm & Migrate
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
// ─────────────────────────────────────────────────────────────────────────────
// State
// ─────────────────────────────────────────────────────────────────────────────
let stagedItems    = [];   // { id, name, patient, date, price, service_id }
let sourceItems    = [];   // items loaded for current source service
let currentBatchId = null;
let pollInterval   = null;

// ─────────────────────────────────────────────────────────────────────────────
// Source Service Change — load appointment-services via AJAX
// ─────────────────────────────────────────────────────────────────────────────
$('#sourceService').on('change', function () {
    const serviceId = $(this).val();

    // Reset
    sourceItems = [];
    $('#selectionToolbar').addClass('d-none');
    $('#moveToStagingBtn').prop('disabled', true);

    if (!serviceId) {
        renderSourceEmpty('Select a source service above to load items.');
        return;
    }

    // Loading state
    $('#sourceList').html(
        '<div class="empty-state"><div class="spinner-border" style="color:var(--brand)"></div>' +
        '<span class="mt-2">Loading…</span></div>'
    );

    $.get('{{ route("taxonomy.appointment-services") }}', { service_id: serviceId })
        .done(function (data) {
            // Exclude already-staged items
            const stagedIds = stagedItems.map(i => i.id);
            sourceItems = data.filter(i => !stagedIds.includes(i.id));
            renderRightPanel();
            $('#selectionToolbar').removeClass('d-none');
        })
        .fail(function () {
            renderSourceEmpty('Failed to load items. Please try again.');
        });
});

// ─────────────────────────────────────────────────────────────────────────────
// Render Right Panel
// ─────────────────────────────────────────────────────────────────────────────
function renderRightPanel() {
    const $list = $('#sourceList');

    if (sourceItems.length === 0) {
        renderSourceEmpty('No items found for this service, or all have been staged.');
        $('#rightCount').text('0 items');
        $('#moveToStagingBtn').prop('disabled', true);
        return;
    }

    let html = '';
    sourceItems.forEach(function (item) {
        html += '<div class="panel-item source-item" data-id="' + item.id + '">' +
            '<input type="checkbox" class="form-check-input source-checkbox me-2 mt-1" value="' + item.id + '">' +
            '<div class="flex-grow-1">' +
                '<div class="item-name">' + escHtml(item.name) + '</div>' +
                '<div class="item-meta">' +
                    '<i class="bi bi-person me-1"></i>' + escHtml(item.patient) +
                    '<span class="ms-3"><i class="bi bi-calendar3 me-1"></i>' + escHtml(item.date) + '</span>' +
                    '<span class="ms-3 fw-semibold" style="color:var(--brand)">Rs&nbsp;' + escHtml(item.price) + '</span>' +
                '</div>' +
            '</div>' +
        '</div>';
    });

    $list.html(html);
    $('#rightCount').text(sourceItems.length + ' item' + (sourceItems.length !== 1 ? 's' : ''));
    updateMoveBtn();
}

function renderSourceEmpty(msg) {
    $('#sourceList').html(
        '<div class="empty-state">' +
            '<i class="bi bi-inbox fs-1 mb-2" style="color:var(--brand);opacity:.4"></i>' +
            '<span>' + escHtml(msg) + '</span>' +
        '</div>'
    );
}

// ─────────────────────────────────────────────────────────────────────────────
// Checkbox / Select All / None
// ─────────────────────────────────────────────────────────────────────────────
$(document).on('change', '.source-checkbox', function () {
    updateMoveBtn();
});

$('#selectAllBtn').on('click', function () {
    $('.source-checkbox').prop('checked', true);
    updateMoveBtn();
});

$('#selectNoneBtn').on('click', function () {
    $('.source-checkbox').prop('checked', false);
    updateMoveBtn();
});

function updateMoveBtn() {
    const anyChecked = $('.source-checkbox:checked').length > 0;
    $('#moveToStagingBtn').prop('disabled', !anyChecked);
}

// ─────────────────────────────────────────────────────────────────────────────
// Move selected items → Staging
// ─────────────────────────────────────────────────────────────────────────────
$('#moveToStagingBtn').on('click', function () {
    const checkedIds = $('.source-checkbox:checked').map(function () {
        return parseInt($(this).val());
    }).get();

    if (checkedIds.length === 0) return;

    // Move from sourceItems → stagedItems
    checkedIds.forEach(function (id) {
        const item = sourceItems.find(i => i.id === id);
        if (item) stagedItems.push(item);
    });

    sourceItems = sourceItems.filter(i => !checkedIds.includes(i.id));

    renderRightPanel();
    renderLeftPanel();
    hideAlerts();
});

// ─────────────────────────────────────────────────────────────────────────────
// Render Left Panel (Staging)
// ─────────────────────────────────────────────────────────────────────────────
function renderLeftPanel() {
    const $list = $('#stagingList');

    if (stagedItems.length === 0) {
        $list.html(
            '<div class="empty-state" id="stagingEmpty">' +
                '<i class="bi bi-arrow-right-circle fs-1 mb-2" style="color:var(--brand);opacity:.4"></i>' +
                '<span>Move items from the left panel to stage them here.</span>' +
            '</div>'
        );
        $('#stagingCount').text('0');
        $('#previewBtn, #migrateBtn').prop('disabled', true);
        return;
    }

    let html = '';
    stagedItems.forEach(function (item) {
        html += '<div class="panel-item staged-item" data-id="' + item.id + '">' +
            '<div class="flex-grow-1">' +
                '<div class="item-name">' + escHtml(item.name) + '</div>' +
                '<div class="item-meta">' +
                    '<i class="bi bi-person me-1"></i>' + escHtml(item.patient) +
                    '<span class="ms-3"><i class="bi bi-calendar3 me-1"></i>' + escHtml(item.date) + '</span>' +
                    '<span class="ms-3 fw-semibold" style="color:var(--brand)">Rs&nbsp;' + escHtml(item.price) + '</span>' +
                '</div>' +
            '</div>' +
            '<button type="button" class="btn btn-sm btn-outline-danger remove-staged ms-2" ' +
                    'data-id="' + item.id + '" title="Remove from staging">' +
                '<i class="bi bi-x"></i>' +
            '</button>' +
        '</div>';
    });

    $list.html(html);
    $('#stagingCount').text(stagedItems.length);
    $('#previewBtn, #migrateBtn').prop('disabled', false);
}

// ─────────────────────────────────────────────────────────────────────────────
// Remove item from Staging → back to Source
// ─────────────────────────────────────────────────────────────────────────────
$(document).on('click', '.remove-staged', function () {
    const id   = parseInt($(this).data('id'));
    const item = stagedItems.find(i => i.id === id);

    if (item) {
        // Add back to right panel only if the source service still matches
        const currentSource = parseInt($('#sourceService').val());
        if (item.service_id === currentSource) {
            sourceItems.push(item);
        }
        stagedItems = stagedItems.filter(i => i.id !== id);
    }

    renderLeftPanel();
    renderRightPanel();
    hideAlerts();
});

// ─────────────────────────────────────────────────────────────────────────────
// Target Service validation helper
// ─────────────────────────────────────────────────────────────────────────────
function validateTarget() {
    const targetId = $('#targetService').val();
    if (!targetId) {
        $('#targetService').addClass('is-invalid');
        $('#targetError').removeClass('d-none');
        return false;
    }
    $('#targetService').removeClass('is-invalid');
    $('#targetError').addClass('d-none');
    return true;
}

$('#targetService').on('change', function () {
    if ($(this).val()) {
        $(this).removeClass('is-invalid');
        $('#targetError').addClass('d-none');
    }
});

// ─────────────────────────────────────────────────────────────────────────────
// Preview Button
// ─────────────────────────────────────────────────────────────────────────────
$('#previewBtn').on('click', function () {
    if (!validateTarget()) return;

    $('#previewBody').html(
        '<div class="text-center py-4"><div class="spinner-border" style="color:var(--brand)"></div></div>'
    );
    $('#previewModal').modal('show');

    $.post('{{ route("taxonomy.preview") }}', {
        _token    : '{{ csrf_token() }}',
        ids       : stagedItems.map(i => i.id),
        target_id : $('#targetService').val()
    })
    .done(function (data) {
        const sourceBadges = data.sources.length
            ? data.sources.map(function (s) {
                return '<span class="badge bg-secondary me-1">' + escHtml(s) + '</span>';
              }).join('')
            : '<span class="text-muted">—</span>';

        $('#previewBody').html(
            '<div class="alert border-0 mb-3" ' +
                 'style="background:var(--brand-light);color:var(--brand);border-left:4px solid var(--brand)!important">' +
                '<i class="bi bi-exclamation-triangle-fill me-2"></i>' +
                'You are about to reassign <strong>' + data.count + ' appointment service(s)</strong>. ' +
                'This <strong>cannot be undone</strong>.' +
            '</div>' +
            '<table class="table table-sm table-bordered mb-0">' +
                '<tr><th class="bg-light" style="width:40%">Items to reassign</th>' +
                    '<td><strong>' + data.count + '</strong></td></tr>' +
                '<tr><th class="bg-light">From service(s)</th>' +
                    '<td>' + sourceBadges + '</td></tr>' +
                '<tr><th class="bg-light">To service</th>' +
                    '<td><strong class="text-success">' + escHtml(data.target) + '</strong></td></tr>' +
            '</table>'
        );
    })
    .fail(function () {
        $('#previewBody').html(
            '<div class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>' +
            'Failed to load preview. Please try again.</div>'
        );
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Migrate Buttons
// ─────────────────────────────────────────────────────────────────────────────
$('#migrateBtn').on('click', function () {
    if (!validateTarget()) return;
    doMigrate();
});

$('#confirmMigrateBtn').on('click', function () {
    $('#previewModal').modal('hide');
    doMigrate();
});

// ─────────────────────────────────────────────────────────────────────────────
// doMigrate — POST to server, start progress polling
// ─────────────────────────────────────────────────────────────────────────────
function doMigrate() {
    const targetId = $('#targetService').val();
    const ids      = stagedItems.map(i => i.id);

    if (!targetId || ids.length === 0) return;

    hideAlerts();

    // Lock UI
    $('#migrateBtn, #previewBtn, #moveToStagingBtn').prop('disabled', true);
    $('select').prop('disabled', true);

    // Show progress
    $('#progressSection').removeClass('d-none');
    setProgressBar(0, ids.length, false);

    $.post('{{ route("taxonomy.migrate") }}', {
        _token    : '{{ csrf_token() }}',
        ids       : ids,
        target_id : targetId
    })
    .done(function (data) {
        currentBatchId = data.batch_id;
        startPolling(ids.length);
    })
    .fail(function () {
        onMigrationError('Failed to start migration. Please try again.');
    });
}

// ─────────────────────────────────────────────────────────────────────────────
// Polling
// ─────────────────────────────────────────────────────────────────────────────
function startPolling(total) {
    pollInterval = setInterval(function () {
        $.get('{{ route("taxonomy.progress") }}', { batch_id: currentBatchId })
            .done(function (data) {
                setProgressBar(data.processed, data.total || total, false);

                if (data.status === 'done') {
                    clearInterval(pollInterval);
                    setProgressBar(data.total, data.total, true);
                    onMigrationDone(data.total);
                } else if (data.status === 'failed') {
                    clearInterval(pollInterval);
                    onMigrationError(data.error || 'Migration failed. Please try again.');
                }
            });
    }, 2000);
}

function setProgressBar(processed, total, done) {
    const pct = total > 0 ? Math.round((processed / total) * 100) : 0;
    const $bar = $('#progressBar');
    $bar.css('width', pct + '%').text(pct + '%');
    if (done) {
        $bar.css('background', '#198754');
        $('#progressLabel').html(
            '<i class="bi bi-check-circle-fill me-2" style="color:#198754"></i>' +
            '<span style="color:#198754">Migration complete!</span>'
        );
    }
    $('#progressText').text(processed + ' / ' + total);
}

function onMigrationDone(count) {
    // Clear state
    const movedCount = count;
    stagedItems  = [];
    sourceItems  = [];

    renderLeftPanel();
    renderRightPanel();

    // Re-enable UI
    $('select').prop('disabled', false);
    $('#targetService').val('');
    $('#sourceService').val('');
    $('#selectionToolbar').addClass('d-none');

    // Show success
    $('#successMessage').text(
        movedCount + ' appointment service(s) successfully reassigned.'
    );
    $('#successAlert').removeClass('d-none');
    $('html, body').animate({ scrollTop: 0 }, 300);

    // Hide progress after 3s
    setTimeout(function () {
        $('#progressSection').addClass('d-none');
        resetProgressBar();
    }, 3000);
}

function onMigrationError(msg) {
    clearInterval(pollInterval);
    $('#progressSection').addClass('d-none');
    resetProgressBar();

    // Re-enable UI
    $('select').prop('disabled', false);
    $('#migrateBtn, #previewBtn').prop('disabled', stagedItems.length === 0);
    if (sourceItems.length > 0) $('#moveToStagingBtn').prop('disabled', false);

    $('#errorMessage').text(msg);
    $('#errorAlert').removeClass('d-none');
    $('html, body').animate({ scrollTop: 0 }, 300);
}

function resetProgressBar() {
    $('#progressBar').css({ width: '0%', background: 'var(--brand)' }).text('0%');
    $('#progressText').text('0 / 0');
    $('#progressLabel').html(
        '<span class="spinner-border spinner-border-sm me-2" style="color:var(--brand)"></span>' +
        'Migration in progress…'
    );
}

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────
function escHtml(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function hideAlerts() {
    $('#successAlert, #errorAlert').addClass('d-none');
}
</script>
@endsection
