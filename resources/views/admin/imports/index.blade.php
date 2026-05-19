@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- ── Page Header ────────────────────────────────────────────────────── --}}
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-file-earmark-arrow-up me-2" style="color:#B1083C"></i>CSV Import
            </h4>
        </div>
        <hr class="my-3">
    </div>

    @include('flash-message')

    @can('imports.create')
    {{-- ══════════════════════════════════════════════════════════════════════
         STEP INDICATOR  (5 steps)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="row mx-1 mb-4">
        <div class="col-12">
            <div class="shadow-css p-3">
                <div class="import-steps d-flex align-items-center justify-content-center gap-0">
                    <div class="imp-step active" id="stepIndicator1">
                        <div class="imp-step-circle">1</div>
                        <div class="imp-step-label">Upload CSV</div>
                    </div>
                    <div class="imp-step-line"></div>
                    <div class="imp-step" id="stepIndicator2">
                        <div class="imp-step-circle">2</div>
                        <div class="imp-step-label">Map Columns</div>
                    </div>
                    <div class="imp-step-line"></div>
                    <div class="imp-step" id="stepIndicator3">
                        <div class="imp-step-circle">3</div>
                        <div class="imp-step-label">Filter Criteria</div>
                    </div>
                    <div class="imp-step-line"></div>
                    <div class="imp-step" id="stepIndicator4">
                        <div class="imp-step-circle">4</div>
                        <div class="imp-step-label">Processing</div>
                    </div>
                    <div class="imp-step-line"></div>
                    <div class="imp-step" id="stepIndicator5">
                        <div class="imp-step-circle">5</div>
                        <div class="imp-step-label">Done</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         STEP 1 — Upload CSV
    ══════════════════════════════════════════════════════════════════════ --}}
    <div id="step1" class="row mx-1 mb-4">
        <div class="col-lg-6 col-12">
            <div class="shadow-css p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-cloud-upload me-2" style="color:#B1083C"></i>Upload CSV File</h6>

                <div class="imp-drop-zone" id="dropZone">
                    <i class="bi bi-file-earmark-spreadsheet imp-drop-icon"></i>
                    <p class="mb-1 fw-semibold">Drag &amp; drop your CSV here</p>
                    <p class="text-muted small mb-3">or click to browse — max 10 MB</p>
                    <input type="file" id="csvFileInput" accept=".csv,text/csv" class="d-none">
                    <button type="button" class="btn btn-theme btn-sm" onclick="document.getElementById('csvFileInput').click()">
                        <i class="bi bi-folder2-open me-1"></i> Choose File
                    </button>
                </div>

                <div id="selectedFileInfo" class="mt-3 d-none">
                    <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#fff5f7;border:1px solid #f5c6d3">
                        <i class="bi bi-file-earmark-text text-theme-color fs-5"></i>
                        <span id="selectedFileName" class="small fw-semibold text-truncate flex-1"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearFileBtn"><i class="bi bi-x"></i></button>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-theme" id="uploadCsvBtn" disabled>
                        <i class="bi bi-upload me-1"></i> Upload &amp; Parse Headers
                        <span id="uploadSpinner" class="spinner-border spinner-border-sm ms-1 d-none"></span>
                    </button>
                </div>
                <div id="uploadError" class="text-danger small mt-2 d-none"></div>
            </div>
        </div>

        <div class="col-lg-6 col-12 mt-3 mt-lg-0">
            <div class="shadow-css p-4 h-100">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2" style="color:#B1083C"></i>How It Works</h6>
                <ol class="small text-muted ps-3 mb-0" style="line-height:1.9">
                    <li>Upload a <strong>CSV file</strong> with appointment data</li>
                    <li>Map your CSV columns to the system fields</li>
                    <li>Set <strong>filter criteria</strong> to narrow down which rows to import</li>
                    <li>The importer will <strong>match or create</strong> patients, appointments, and services automatically</li>
                    <li>Track progress in real time</li>
                    <li>Download failed rows and roll back if needed</li>
                </ol>
                <hr class="my-3">
                <p class="small fw-semibold mb-1">Required fields (at least):</p>
                <div class="d-flex flex-wrap gap-1">
                    <span class="badge bg-danger">Patient Phone <em>or</em> Email</span>
                    <span class="badge bg-danger">Appointment Date</span>
                    <span class="badge bg-danger">Service Name</span>
                </div>
                <p class="small fw-semibold mb-1 mt-2">Optional fields:</p>
                <div class="d-flex flex-wrap gap-1">
                    <span class="badge bg-secondary">Patient Name</span>
                    <span class="badge bg-secondary">Price</span>
                    <span class="badge bg-secondary">Discounted Price</span>
                    <span class="badge bg-secondary">Discount</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         STEP 2 — Column Mapping
    ══════════════════════════════════════════════════════════════════════ --}}
    <div id="step2" class="row mx-1 mb-4 d-none">
        <div class="col-12">
            <div class="shadow-css p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-diagram-3 me-2" style="color:#B1083C"></i>Map Columns</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="backToStep1Btn">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </button>
                </div>
                <p class="text-muted small mb-3">
                    File: <strong id="mappingFilename"></strong> — Match each system field to the corresponding column in your CSV.
                    Required fields are marked <span class="text-danger">*</span>
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" style="min-width:500px">
                        <thead>
                            <tr>
                                <th style="background:#B1083C;color:#fff;width:40%">System Field</th>
                                <th style="background:#B1083C;color:#fff">CSV Column</th>
                            </tr>
                        </thead>
                        <tbody id="mappingTableBody">
                            {{-- Populated by JS --}}
                        </tbody>
                    </table>
                </div>

                <div id="mappingError" class="alert alert-danger d-none mt-2"></div>

                <div class="mt-3 d-flex gap-2">
                    <button type="button" class="btn btn-theme" id="nextToFiltersBtn">
                        <i class="bi bi-funnel me-1"></i> Next: Set Filters
                        <span id="nextFiltersSpinner" class="spinner-border spinner-border-sm ms-1 d-none"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         STEP 3 — Search Criteria
    ══════════════════════════════════════════════════════════════════════ --}}
    <div id="step3" class="row mx-1 mb-4 d-none">
        <div class="col-lg-8 col-12">
            <div class="shadow-css p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-search me-2" style="color:#B1083C"></i>Search Criteria
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="backToStep2Btn">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </button>
                </div>
                <p class="text-muted small mb-4">
                    Define how to find existing records in each table before creating new ones.
                    Match your <strong>CSV file columns</strong> against the <strong>database table columns</strong>.
                    You can add multiple conditions per table — all are combined with AND logic.
                </p>

                {{-- ── Users Table ──────────────────────────────────────── --}}
                <div class="sc-table-block mb-4">
                    <div class="sc-table-header">
                        <i class="bi bi-people me-2"></i>Users Table
                        <span class="badge bg-secondary ms-2 fw-normal" style="font-size:.7rem">find existing patient by</span>
                    </div>
                    <div id="usersCriteriaRows" class="sc-rows-wrap"></div>
                    <button type="button" class="btn btn-sm btn-outline-theme mt-2" onclick="addCriteriaRow('users')">
                        <i class="bi bi-plus-lg me-1"></i> Add Condition
                    </button>
                </div>

                {{-- ── Categories Table ─────────────────────────────────── --}}
                <div class="sc-table-block mb-4">
                    <div class="sc-table-header">
                        <i class="bi bi-tags me-2"></i>Categories Table
                        <span class="badge bg-secondary ms-2 fw-normal" style="font-size:.7rem">find existing service/category by</span>
                    </div>
                    <div id="categoriesCriteriaRows" class="sc-rows-wrap"></div>
                    <button type="button" class="btn btn-sm btn-outline-theme mt-2" onclick="addCriteriaRow('categories')">
                        <i class="bi bi-plus-lg me-1"></i> Add Condition
                    </button>
                </div>

                {{-- ── Appointments Table ───────────────────────────────── --}}
                <div class="sc-table-block mb-4">
                    <div class="sc-table-header">
                        <i class="bi bi-calendar3 me-2"></i>Appointments Table
                        <span class="badge bg-secondary ms-2 fw-normal" style="font-size:.7rem">find existing appointment by</span>
                    </div>
                    {{-- Auto-injected --}}
                    <div class="sc-auto-row">
                        <i class="bi bi-lock-fill me-1 text-muted"></i>
                        <span class="text-muted small"><strong>user_id</strong> — auto-injected from Users result above</span>
                    </div>
                    <div id="appointmentsCriteriaRows" class="sc-rows-wrap"></div>
                    <button type="button" class="btn btn-sm btn-outline-theme mt-2" onclick="addCriteriaRow('appointments')">
                        <i class="bi bi-plus-lg me-1"></i> Add Condition
                    </button>
                </div>

                <div id="criteriaError" class="alert alert-danger d-none mt-2"></div>

                <div class="d-flex gap-2 flex-wrap mt-3">
                    <button type="button" class="btn btn-theme" id="confirmImportBtn">
                        <i class="bi bi-play-fill me-1"></i> Confirm &amp; Start Import
                        <span id="confirmSpinner" class="spinner-border spinner-border-sm ms-1 d-none"></span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="clearCriteriaBtn">
                        <i class="bi bi-x-circle me-1"></i> Clear All
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12 mt-3 mt-lg-0">
            <div class="shadow-css p-4 h-100">
                <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb me-2" style="color:#B1083C"></i>How It Works</h6>
                <ul class="small text-muted ps-3 mb-0" style="line-height:1.9">
                    <li>Left dropdown = <strong>your CSV file column</strong> (headers from uploaded file).</li>
                    <li>Right dropdown = <strong>database table column</strong> to match against.</li>
                    <li>Multiple conditions = <strong>AND</strong> logic (all must match).</li>
                    <li>If a record is <strong>found</strong> → its ID is reused.</li>
                    <li>If <strong>not found</strong> → a new record is created automatically.</li>
                    <li><strong>user_id</strong> in Appointments is always auto-filled from the Users result — no need to add it manually.</li>
                </ul>
                <hr class="my-3">
                <p class="small fw-semibold mb-1">Available DB columns per table:</p>
                <div class="small text-muted">
                    <div><strong>Users:</strong> phone, email, name</div>
                    <div class="mt-1"><strong>Categories:</strong> name, slug</div>
                    <div class="mt-1"><strong>Appointments:</strong> date, doctor_id, clinic_id</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         STEP 4 — Progress  (was step 3)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div id="step4" class="row mx-1 mb-4 d-none">
        <div class="col-lg-8 col-12">
            <div class="shadow-css p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-hourglass-split me-2" style="color:#B1083C"></i>Importing…</h6>

                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Progress</span>
                    <span id="progressText">0 / 0</span>
                </div>
                <div class="progress mb-3" style="height:20px;border-radius:10px">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" style="width:0%;background:#B1083C" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>

                <div class="row g-2 text-center">
                    <div class="col-3">
                        <div class="p-2 rounded" style="background:#f8f9fa">
                            <div class="fs-4 fw-bold text-theme-color" id="statProcessed">0</div>
                            <div class="small text-muted">Processed</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded" style="background:#f0fff4">
                            <div class="fs-4 fw-bold text-success" id="statImported">0</div>
                            <div class="small text-muted">Imported</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded" style="background:#fff5f5">
                            <div class="fs-4 fw-bold text-danger" id="statFailed">0</div>
                            <div class="small text-muted">Failed</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded" style="background:#fff8e1">
                            <div class="fs-4 fw-bold text-warning" id="statSkipped">0</div>
                            <div class="small text-muted">Skipped</div>
                        </div>
                    </div>
                </div>

                <p class="text-muted small mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Please keep this page open until the import completes.</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         STEP 5 — Summary  (was step 4)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div id="step5" class="row mx-1 mb-4 d-none">
        <div class="col-lg-8 col-12">
            <div class="shadow-css p-4">
                <div class="text-center mb-4">
                    <div id="summaryIcon" class="fs-1 mb-2"></div>
                    <h5 id="summaryTitle" class="fw-bold"></h5>
                    <p id="summarySubtitle" class="text-muted small"></p>
                </div>

                <div class="row g-2 text-center mb-4">
                    <div class="col-3">
                        <div class="p-3 rounded" style="background:#f8f9fa">
                            <div class="fs-3 fw-bold text-theme-color" id="sumTotal">0</div>
                            <div class="small text-muted">Total Rows</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3 rounded" style="background:#f0fff4">
                            <div class="fs-3 fw-bold text-success" id="sumImported">0</div>
                            <div class="small text-muted">Imported</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3 rounded" style="background:#fff5f5">
                            <div class="fs-3 fw-bold text-danger" id="sumFailed">0</div>
                            <div class="small text-muted">Failed</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3 rounded" style="background:#fff8e1">
                            <div class="fs-3 fw-bold text-warning" id="sumSkipped">0</div>
                            <div class="small text-muted">Skipped</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <button type="button" class="btn btn-theme" id="newImportBtn">
                        <i class="bi bi-plus-lg me-1"></i> New Import
                    </button>
                    <a id="downloadFailedBtn" href="#" class="btn btn-outline-warning d-none">
                        <i class="bi bi-download me-1"></i> Download Failed Rows
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endcan

    {{-- ══════════════════════════════════════════════════════════════════════
         IMPORT HISTORY — DataTable
    ══════════════════════════════════════════════════════════════════════ --}}
    @can('imports.view')
    <div class="row mx-1 mb-4">
        <div class="col-12">
            <div class="shadow-css p-3">
                <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2" style="color:#B1083C"></i>Import History</h6>
                <table id="importsTable" class="table table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Filename</th>
                            <th>Total</th>
                            <th>Imported</th>
                            <th>Failed</th>
                            <th>Status</th>
                            <th>Uploaded By</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @endcan

</div>
@endsection

@section('script')
<style>
    .text-theme-color { color: #B1083C; }
    .btn-theme        { background: linear-gradient(90deg,#B1083C,#d13729); color:#fff; border:none; }
    .btn-theme:hover  { background: linear-gradient(90deg,#9a0635,#b02d1f); color:#fff; }
    .shadow-css       { background:#fff; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,.08); }

    /* ── Step indicator ─────────────────────────────────────────── */
    .import-steps { flex-wrap: wrap; }
    .imp-step { display:flex; flex-direction:column; align-items:center; gap:4px; }
    .imp-step-circle {
        width:36px; height:36px; border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        font-weight:700; font-size:.85rem;
        background:#e9ecef; color:#6c757d;
        border: 2px solid #dee2e6;
        transition: all .3s;
    }
    .imp-step.active   .imp-step-circle { background:#B1083C; color:#fff; border-color:#B1083C; }
    .imp-step.done     .imp-step-circle { background:#198754; color:#fff; border-color:#198754; }
    .imp-step-label { font-size:.72rem; color:#6c757d; white-space:nowrap; }
    .imp-step.active .imp-step-label { color:#B1083C; font-weight:600; }
    .imp-step.done   .imp-step-label { color:#198754; font-weight:600; }
    .imp-step-line { flex:1; height:2px; background:#dee2e6; min-width:24px; max-width:60px; margin-bottom:16px; }

    /* ── Drop zone ──────────────────────────────────────────────── */
    .imp-drop-zone {
        border: 2px dashed #B1083C;
        border-radius: 10px;
        padding: 32px 20px;
        text-align: center;
        cursor: pointer;
        background: #fff5f7;
        transition: background .2s;
    }
    .imp-drop-zone.drag-over { background: #fde8ee; }
    .imp-drop-icon { font-size: 2.5rem; color: #B1083C; margin-bottom: 8px; display: block; }

    /* ── Mapping table selects ──────────────────────────────────── */
    .mapping-select + .select2-container .select2-selection--single {
        height: 34px; border-color: #ced4da; border-radius: 6px;
        display:flex; align-items:center;
    }
    .mapping-select + .select2-container .select2-selection__rendered { line-height:34px; padding-left:8px; }
    .mapping-select + .select2-container .select2-selection__arrow    { height:32px; }
    .select2-container--default .select2-results__option--highlighted { background:#B1083C !important; }
    .select2-dropdown { border-color:#B1083C !important; }
    .field-required td:first-child { border-left: 3px solid #B1083C; }

    /* ── Search Criteria step ───────────────────────────────────── */
    .sc-table-block { border:1px solid #e9ecef; border-radius:8px; padding:16px; }
    .sc-table-header {
        font-weight:700; font-size:.85rem; color:#B1083C;
        margin-bottom:12px; padding-bottom:8px;
        border-bottom:2px solid #f0e0e5;
    }
    .sc-auto-row {
        background:#f8f9fa; border-radius:6px;
        padding:7px 12px; margin-bottom:8px;
        display:flex; align-items:center; gap:6px;
        font-size:.83rem;
    }
    .sc-condition-row {
        display:flex; align-items:center; gap:8px;
        margin-bottom:8px; flex-wrap:wrap;
    }
    .sc-condition-row .sc-select {
        flex:1; min-width:140px;
    }
    .sc-condition-row .sc-eq {
        font-weight:700; color:#6c757d; font-size:.9rem;
        padding:0 4px; white-space:nowrap;
    }
    .sc-condition-row .btn-remove-cond {
        flex-shrink:0; padding:3px 8px;
    }
    .sc-rows-wrap { min-height:8px; }
    .btn-outline-theme { border-color:#B1083C; color:#B1083C; }
    .btn-outline-theme:hover { background:#B1083C; color:#fff; }

    /* ── DataTable header ───────────────────────────────────────── */
    #importsTable thead th { background:linear-gradient(90deg,#B1083C,#d13729); color:#fff; border:none; white-space:nowrap; }
</style>

<script>
$(window).on('load', function () {

    /* ═══════════════════════════════════════════════════════
       STATE
    ═══════════════════════════════════════════════════════ */
    let currentImportLogId = null;
    let pollInterval       = null;
    let csvColumns         = [];
    let selectedFile       = null;
    let currentMapping     = {};   // saved when moving step 2 → 3

    // Logical target fields
    const TARGET_FIELDS = [
        { key: 'patient_phone',            label: 'Patient Phone',             required: false },
        { key: 'patient_email',            label: 'Patient Email',             required: false },
        { key: 'patient_name',             label: 'Patient Name',              required: false },
        { key: 'appointment_date',         label: 'Appointment Date',          required: true  },
        { key: 'service_name',             label: 'Service Name',              required: true  },
        { key: 'service_price',            label: 'Service Price',             required: false },
        { key: 'service_discounted_price', label: 'Service Discounted Price',  required: false },
        { key: 'service_discount',         label: 'Service Discount',          required: false },
    ];

    /* ═══════════════════════════════════════════════════════
       STEP NAVIGATION  (5 steps)
    ═══════════════════════════════════════════════════════ */
    function goToStep(n) {
        for (let i = 1; i <= 5; i++) {
            $('#step' + i).addClass('d-none');
            const ind = $('#stepIndicator' + i);
            ind.removeClass('active done');
            if (i < n)  ind.addClass('done');
            if (i === n) ind.addClass('active');
        }
        $('#step' + n).removeClass('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /* ═══════════════════════════════════════════════════════
       STEP 1 — Upload
    ═══════════════════════════════════════════════════════ */
    const dropZone = document.getElementById('dropZone');
    ['dragenter','dragover'].forEach(ev => dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.add('drag-over'); }));
    ['dragleave','drop'].forEach(ev  => dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.remove('drag-over'); }));
    dropZone.addEventListener('drop',  e => setFile(e.dataTransfer.files[0]));
    dropZone.addEventListener('click', () => document.getElementById('csvFileInput').click());

    $('#csvFileInput').on('change', function () { if (this.files[0]) setFile(this.files[0]); });

    function setFile(file) {
        if (!file || !file.name.match(/\.csv$/i)) {
            showUploadError('Please select a valid .csv file.'); return;
        }
        selectedFile = file;
        $('#selectedFileName').text(file.name);
        $('#selectedFileInfo').removeClass('d-none');
        $('#uploadCsvBtn').prop('disabled', false);
        hideUploadError();
    }

    $('#clearFileBtn').on('click', function () {
        selectedFile = null;
        $('#csvFileInput').val('');
        $('#selectedFileInfo').addClass('d-none');
        $('#uploadCsvBtn').prop('disabled', true);
    });

    $('#uploadCsvBtn').on('click', function () {
        if (!selectedFile) return;
        const fd = new FormData();
        fd.append('csv_file', selectedFile);
        fd.append('_token', '{{ csrf_token() }}');

        $('#uploadSpinner').removeClass('d-none');
        $(this).prop('disabled', true);
        hideUploadError();

        $.ajax({
            url: '{{ route("imports.upload") }}',
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (res) {
                currentImportLogId = res.import_log_id;
                csvColumns         = res.csv_columns;
                $('#mappingFilename').text(res.filename);
                buildMappingTable();
                goToStep(2);
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.errors?.csv_file?.[0] ?? xhr.responseJSON?.message ?? 'Upload failed.';
                showUploadError(msg);
            },
            complete: function () {
                $('#uploadSpinner').addClass('d-none');
                $('#uploadCsvBtn').prop('disabled', false);
            }
        });
    });

    function showUploadError(msg) { $('#uploadError').text(msg).removeClass('d-none'); }
    function hideUploadError()    { $('#uploadError').addClass('d-none'); }

    $('#backToStep1Btn').on('click', () => goToStep(1));

    /* ═══════════════════════════════════════════════════════
       STEP 2 — Column Mapping
    ═══════════════════════════════════════════════════════ */
    function buildMappingTable() {
        const tbody = $('#mappingTableBody').empty();

        TARGET_FIELDS.forEach(field => {
            const autoMatch = csvColumns.find(col =>
                col.toLowerCase().replace(/[^a-z0-9]/g,'') ===
                field.key.toLowerCase().replace(/[^a-z0-9]/g,'') ||
                col.toLowerCase().replace(/[^a-z0-9]/g,'') ===
                field.label.toLowerCase().replace(/[^a-z0-9]/g,'')
            ) ?? '';

            const reqBadge = field.required ? ' <span class="text-danger">*</span>' : '';
            const selectId = 'map_' + field.key;

            let options = '<option value="">— skip —</option>';
            csvColumns.forEach(col => {
                const sel = col === autoMatch ? ' selected' : '';
                options += `<option value="${col}"${sel}>${col}</option>`;
            });

            tbody.append(`
                <tr class="${field.required ? 'field-required' : ''}">
                    <td class="fw-semibold">${field.label}${reqBadge}</td>
                    <td>
                        <select id="${selectId}" name="${field.key}" class="mapping-select w-100" style="width:100%">
                            ${options}
                        </select>
                    </td>
                </tr>
            `);
        });

        $('.mapping-select').select2({
            placeholder: '— skip —',
            allowClear: true,
            width: '100%',
        }).on('select2:select select2:clear', refreshUsedOptions);

        refreshUsedOptions();
    }

    function getUsedValues() {
        const used = [];
        $('.mapping-select').each(function () { const v = $(this).val(); if (v) used.push(v); });
        return used;
    }

    function refreshUsedOptions() {
        const used = getUsedValues();
        $('.mapping-select').each(function () {
            const myVal = $(this).val();
            $(this).find('option').each(function () {
                const v = $(this).val();
                if (v && v !== myVal && used.includes(v)) $(this).prop('disabled', true);
                else $(this).prop('disabled', false);
            });
        });
    }

    // "Next: Set Filters" — validate mapping then advance to Step 3
    $('#nextToFiltersBtn').on('click', function () {
        $('#mappingError').addClass('d-none');

        const mapping = {};
        TARGET_FIELDS.forEach(field => {
            const val = $('#map_' + field.key).val();
            if (val) mapping[field.key] = val;
        });

        if (!mapping.patient_phone && !mapping.patient_email) {
            $('#mappingError').text('Please map at least Patient Phone or Patient Email.').removeClass('d-none');
            return;
        }
        if (!mapping.appointment_date) {
            $('#mappingError').text('Appointment Date is required.').removeClass('d-none');
            return;
        }
        if (!mapping.service_name) {
            $('#mappingError').text('Service Name is required.').removeClass('d-none');
            return;
        }

        currentMapping = mapping;   // save for step 3 → confirm
        initSearchCriteria();       // pre-fill default criteria rows
        goToStep(3);
    });

    /* ═══════════════════════════════════════════════════════
       STEP 3 — Search Criteria
    ═══════════════════════════════════════════════════════ */

    // DB columns available per table
    const DB_COLUMNS = {
        users: [
            { value: 'phone', label: 'phone' },
            { value: 'email', label: 'email' },
            { value: 'name',  label: 'name'  },
        ],
        categories: [
            { value: 'name', label: 'name' },
            { value: 'slug', label: 'slug' },
        ],
        appointments: [
            { value: 'date',      label: 'date'      },
            { value: 'doctor_id', label: 'doctor_id' },
            { value: 'clinic_id', label: 'clinic_id' },
        ],
    };

    // Expose addCriteriaRow globally so inline onclick works
    window.addCriteriaRow = function(table, csvDefault, dbDefault) {
        const csvOpts = csvColumns.map(col =>
            `<option value="${col}"${col === csvDefault ? ' selected' : ''}>${col}</option>`
        ).join('');

        const dbOpts = (DB_COLUMNS[table] || []).map(c =>
            `<option value="${c.value}"${c.value === dbDefault ? ' selected' : ''}>${c.label}</option>`
        ).join('');

        const rowId = 'scrow_' + table + '_' + Date.now();

        const html = `
            <div class="sc-condition-row" id="${rowId}">
                <select class="form-select form-select-sm sc-select sc-csv-col" data-table="${table}">
                    <option value="">— CSV column —</option>
                    ${csvOpts}
                </select>
                <span class="sc-eq">=</span>
                <select class="form-select form-select-sm sc-select sc-db-col" data-table="${table}">
                    <option value="">— DB column —</option>
                    ${dbOpts}
                </select>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-cond"
                        onclick="document.getElementById('${rowId}').remove()">
                    <i class="bi bi-x"></i>
                </button>
            </div>`;

        $('#' + table + 'CriteriaRows').append(html);
    };

    $('#backToStep2Btn').on('click', () => goToStep(2));

    // Pre-fill default conditions when entering step 3
    function initSearchCriteria() {
        // Clear existing
        $('#usersCriteriaRows, #categoriesCriteriaRows, #appointmentsCriteriaRows').empty();

        // Sensible defaults using first available csv column
        const firstCol = csvColumns[0] ?? '';
        addCriteriaRow('users',        firstCol, 'phone');
        addCriteriaRow('categories',   firstCol, 'name');
        addCriteriaRow('appointments', firstCol, 'date');
    }

    // Collect all criteria from UI
    function collectSearchCriteria() {
        const criteria = { users: [], categories: [], appointments: [] };

        ['users', 'categories', 'appointments'].forEach(table => {
            $('#' + table + 'CriteriaRows .sc-condition-row').each(function () {
                const csvCol = $(this).find('.sc-csv-col').val();
                const dbCol  = $(this).find('.sc-db-col').val();
                if (csvCol && dbCol) {
                    criteria[table].push({ csv_column: csvCol, db_column: dbCol });
                }
            });
        });

        return criteria;
    }

    $('#clearCriteriaBtn').on('click', function () {
        $('#usersCriteriaRows, #categoriesCriteriaRows, #appointmentsCriteriaRows').empty();
    });

    // Confirm & Start Import
    $('#confirmImportBtn').on('click', function () {
        $('#criteriaError').addClass('d-none');

        const searchCriteria = collectSearchCriteria();

        $('#confirmSpinner').removeClass('d-none');
        $(this).prop('disabled', true);

        $.ajax({
            url: '{{ route("imports.start") }}',
            method: 'POST',
            data: {
                _token:          '{{ csrf_token() }}',
                import_log_id:   currentImportLogId,
                mapping:         currentMapping,
                search_criteria: searchCriteria,
            },
            success: function (res) {
                goToStep(4);
                startPolling(res.batch_id);
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.error ?? 'Could not start import.';
                $('#criteriaError').text(msg).removeClass('d-none');
                $('#confirmSpinner').addClass('d-none');
                $('#confirmImportBtn').prop('disabled', false);
            }
        });
    });

    /* ═══════════════════════════════════════════════════════
       STEP 4 — Progress polling
    ═══════════════════════════════════════════════════════ */
    function startPolling(batchId) {
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => fetchProgress(batchId), 2000);
        fetchProgress(batchId);
    }

    function fetchProgress(batchId) {
        $.get('{{ route("imports.progress") }}', { import_log_id: batchId }, function (data) {
            const total     = data.total     || 0;
            const processed = data.processed || 0;
            const imported  = data.imported  || 0;
            const failed    = data.failed    || 0;
            const skipped   = data.skipped   || 0;
            const pct       = total > 0 ? Math.round((processed / total) * 100) : 0;

            $('#progressBar').css('width', pct + '%').text(pct + '%').attr('aria-valuenow', pct);
            $('#progressText').text(processed + ' / ' + total);
            $('#statProcessed').text(processed);
            $('#statImported').text(imported);
            $('#statFailed').text(failed);
            $('#statSkipped').text(skipped);

            if (data.status === 'completed' || data.status === 'failed') {
                clearInterval(pollInterval);
                showSummary(batchId, data);
            }
        });
    }

    /* ═══════════════════════════════════════════════════════
       STEP 5 — Summary
    ═══════════════════════════════════════════════════════ */
    function showSummary(batchId, data) {
        const success = data.status === 'completed';
        $('#summaryIcon').html(success
            ? '<i class="bi bi-check-circle-fill text-success"></i>'
            : '<i class="bi bi-x-circle-fill text-danger"></i>');
        $('#summaryTitle').text(success ? 'Import Completed!' : 'Import Failed');
        $('#summarySubtitle').text(success
            ? 'Your data has been processed successfully.'
            : 'An error occurred during import. Check failed rows below.');
        $('#sumTotal').text(data.total    ?? 0);
        $('#sumImported').text(data.imported ?? 0);
        $('#sumFailed').text(data.failed   ?? 0);
        $('#sumSkipped').text(data.skipped  ?? 0);

        if ((data.failed ?? 0) > 0) {
            $('#downloadFailedBtn').attr('href', '/imports/' + batchId + '/download-failed').removeClass('d-none');
        } else {
            $('#downloadFailedBtn').addClass('d-none');
        }

        goToStep(5);
        if ($.fn.DataTable.isDataTable('#importsTable')) {
            $('#importsTable').DataTable().ajax.reload(null, false);
        }
    }

    /* ═══════════════════════════════════════════════════════
       NEW IMPORT RESET
    ═══════════════════════════════════════════════════════ */
    $('#newImportBtn').on('click', function () {
        currentImportLogId = null;
        currentMapping     = {};
        csvColumns         = [];
        selectedFile       = null;
        $('#csvFileInput').val('');
        $('#selectedFileInfo').addClass('d-none');
        $('#uploadCsvBtn').prop('disabled', true);
        $('#usersCriteriaRows, #categoriesCriteriaRows, #appointmentsCriteriaRows').empty();
        hideUploadError();
        goToStep(1);
    });

    /* ═══════════════════════════════════════════════════════
       DATATABLE — Import History
    ═══════════════════════════════════════════════════════ */
    @can('imports.view')
    var table = $('#importsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("imports.index") }}',
        columns: [
            { data: 'DT_RowIndex',          name: 'DT_RowIndex',         orderable: false, searchable: false, width: '50px' },
            { data: 'filename',             name: 'filename' },
            { data: 'total_rows',           name: 'total_rows',          searchable: false },
            { data: 'imported_count',       name: 'imported_count',      searchable: false },
            { data: 'failed_count',         name: 'failed_count',        searchable: false },
            { data: 'status_badge',         name: 'status',              orderable: false, searchable: false },
            { data: 'uploader_name',        name: 'uploader_name',       orderable: false },
            { data: 'created_at_formatted', name: 'created_at',          searchable: false },
            { data: 'action',               name: 'action',              orderable: false, searchable: false, className: 'text-center' },
        ],
        order: [[7, 'desc']],
        responsive: true,
        pageLength: 15,
        language: {
            searchPlaceholder: 'Search by filename or uploader…',
            processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Loading…',
        },
    });

    /* ── Rollback ── */
    $(document).on('click', '.btn-rollback', function () {
        const id    = $(this).data('id');
        const token = $(this).data('token');
        const btn   = $(this);

        Swal.fire({
            title: 'Rollback this import?',
            text:  'All appointment services inserted by this import will be permanently deleted.',
            icon:  'warning',
            showCancelButton: true,
            confirmButtonColor: '#B1083C',
            cancelButtonColor:  '#6c757d',
            confirmButtonText:  '<i class="bi bi-arrow-counterclockwise me-1"></i> Yes, Rollback',
            cancelButtonText:   'Cancel',
            reverseButtons: true,
        }).then(result => {
            if (!result.isConfirmed) return;
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            $.ajax({
                url:    '/imports/' + id + '/rollback',
                method: 'POST',
                data:   { _token: token },
                success: function () {
                    table.ajax.reload(null, false);
                    Swal.fire({ icon:'success', title:'Rolled back', timer:2000, timerProgressBar:true,
                        showConfirmButton:false, confirmButtonColor:'#B1083C' });
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html('<i class="bi bi-arrow-counterclockwise"></i>');
                    Swal.fire({ icon:'error', title:'Error', text: xhr.responseJSON?.error ?? 'Rollback failed.',
                        confirmButtonColor:'#B1083C' });
                }
            });
        });
    });

    /* ── Delete ── */
    $(document).on('click', '.btn-delete-import', function () {
        const id    = $(this).data('id');
        const token = $(this).data('token');
        const btn   = $(this);

        Swal.fire({
            title: 'Delete this import log?',
            text:  'The log record and original CSV file will be removed. Imported data is NOT deleted.',
            icon:  'question',
            showCancelButton: true,
            confirmButtonColor: '#B1083C',
            cancelButtonColor:  '#6c757d',
            confirmButtonText:  'Yes, delete',
            reverseButtons: true,
        }).then(result => {
            if (!result.isConfirmed) return;
            btn.prop('disabled', true);
            $.ajax({
                url:    '/imports/' + id,
                method: 'POST',
                data:   { _method: 'DELETE', _token: token },
                success: function () {
                    table.ajax.reload(null, false);
                    Swal.fire({ icon:'success', title:'Deleted', timer:1800,
                        timerProgressBar:true, showConfirmButton:false, confirmButtonColor:'#B1083C' });
                },
                error: function () {
                    btn.prop('disabled', false);
                    Swal.fire({ icon:'error', title:'Error', text:'Could not delete.', confirmButtonColor:'#B1083C' });
                }
            });
        });
    });
    @endcan

});
</script>
@endsection
