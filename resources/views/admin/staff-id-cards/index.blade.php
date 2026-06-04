@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="row pt-3 mx-1 align-items-center mb-3">
        <div class="col-12 d-flex align-items-center gap-3">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-person-badge me-2" style="color:#B1083C"></i>Staff ID Card Generator
            </h4>
            <button class="btn btn-sm btn-danger ms-auto d-none" id="printBtn" onclick="triggerPrint()">
                <i class="bi bi-printer me-1"></i>Print Cards
            </button>
        </div>
        <hr class="mt-2 mb-0">
    </div>

    <div class="row mx-1 g-3">

        {{-- ════════════════ LEFT PANEL ════════════════ --}}
        <div class="col-lg-4 col-12" id="editorCol">

            {{-- Step 1: Select Clinic & Role --}}
            <div class="wc-form-card mb-3">
                <div class="wc-section-head"><i class="bi bi-funnel me-2"></i>Step 1 — Filter Staff</div>
                <div class="wc-section-body">

                    {{-- Clinic (visible to all; super admin sees all, others see theirs) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Clinic <span class="text-danger">*</span></label>
                        <select id="sel-clinic" class="form-select border-secondary">
                            <option value="">— Select Clinic —</option>
                            @foreach($clinics as $clinic)
                                <option value="{{ $clinic->id }}" data-name="{{ $clinic->name }}">{{ $clinic->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Role --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <select id="sel-role" class="form-select border-secondary" disabled>
                            <option value="">— Select Role —</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-danger btn-sm w-100" id="fetchBtn" disabled onclick="fetchUsers()">
                        <i class="bi bi-search me-1"></i>Load Staff
                    </button>
                </div>
            </div>

            {{-- Step 2: Select Users --}}
            <div class="wc-form-card mb-3" id="userSelectCard" style="display:none">
                <div class="wc-section-head d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-people me-2"></i>Step 2 — Select Staff</span>
                    <span class="badge bg-white text-danger" id="selCount">0 selected</span>
                </div>
                <div class="wc-section-body p-0">

                    {{-- Search bar --}}
                    <div class="px-3 pt-3 pb-2">
                        <input type="text" id="userSearch" class="form-control form-control-sm border-secondary" placeholder="Search by name…">
                    </div>

                    {{-- Select All / None --}}
                    <div class="px-3 pb-2 d-flex gap-2">
                        <button class="btn btn-outline-danger btn-sm" onclick="selectAll()">
                            <i class="bi bi-check2-all me-1"></i>Select All
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="selectNone()">
                            <i class="bi bi-x-circle me-1"></i>Select None
                        </button>
                    </div>

                    <div id="userList" style="max-height:320px;overflow-y:auto;border-top:1px solid #f3f4f6"></div>

                    <div id="noUsersMsg" class="text-center text-muted py-4 small d-none">
                        <i class="bi bi-person-slash d-block fs-3 mb-2"></i>No active staff found for this filter
                    </div>

                    <div class="p-3 border-top" id="generateWrap" style="display:none">
                        <button class="btn btn-danger w-100" onclick="generateCards()" id="generateBtn">
                            <i class="bi bi-credit-card me-1"></i>Generate Cards
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- ════════════════ RIGHT PANEL ════════════════ --}}
        <div class="col-lg-8 col-12">
            <div id="previewEmpty" class="wc-form-card p-5 text-center text-muted">
                <i class="bi bi-person-badge d-block" style="font-size:3rem;color:#e5e7eb;margin-bottom:12px"></i>
                <p class="mb-1 fw-semibold">No cards generated yet</p>
                <small>Select a clinic and role, pick staff members, then click "Generate Cards"</small>
            </div>
            <div id="cardsOutput" style="display:none">
                <div id="cardsGrid" class="d-flex flex-wrap gap-4 justify-content-start"></div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
@include('admin.whatsapp-campaign._styles')

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<style>
/* ─── User list rows ─────────────────────────────────────────────── */
.usr-row {
    display:flex; align-items:center; gap:10px;
    padding:10px 14px; cursor:pointer;
    border-bottom:1px solid #f3f4f6;
    transition:background .1s;
    user-select:none;
}
.usr-row:hover { background:#fef2f2; }
.usr-row.selected { background:rgba(177,8,60,.05); }
.usr-row .usr-check {
    width:18px; height:18px; border-radius:4px;
    border:2px solid #d1d5db; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    transition:all .15s;
}
.usr-row.selected .usr-check { background:#B1083C; border-color:#B1083C; }
.usr-row.selected .usr-check::after { content:'✓'; color:#fff; font-size:.65rem; font-weight:700; }
.usr-avatar {
    width:38px; height:38px; border-radius:8px;
    object-fit:cover; border:1.5px solid #B1083C; flex-shrink:0;
}
.usr-avatar-ph {
    width:38px; height:38px; border-radius:8px;
    background:#f3f4f6; border:1.5px solid #e5e7eb;
    display:flex; align-items:center; justify-content:center;
    font-size:1.1rem; flex-shrink:0;
}
.usr-info { flex:1; min-width:0; }
.usr-info strong { display:block; font-size:.82rem; color:#1a1a2e; }
.usr-info small { font-size:.7rem; color:#9ca3af; }

/* ─── ID Cards ───────────────────────────────────────────────────── */
.id-card-set { display:flex; flex-direction:column; gap:10px; align-items:center; }
.id-card-label { font-size:.72rem; color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:.5px; }

.id-card {
    width:323px; height:204px;
    border-radius:12px;
    position:relative;
    overflow:hidden;
    box-shadow:0 8px 28px rgba(0,0,0,.18);
    flex-shrink:0;
}

/* FRONT */
.card-front { background:#fff; display:flex; flex-direction:column; }

.cf-header {
    background:linear-gradient(135deg,#B1083C 0%,#d13729 60%,#8a0630 100%);
    padding:8px 11px;
    display:flex; align-items:center; gap:8px;
    flex-shrink:0;
}
.cf-logo {
    width:28px; height:28px; border-radius:6px;
    background:rgba(255,255,255,.18);
    display:flex; align-items:center; justify-content:center;
    font-size:.55rem; font-weight:900; color:#fff;
    letter-spacing:-.5px; text-align:center; line-height:1.1;
    flex-shrink:0;
}
.cf-header-text { flex:1; color:#fff; overflow:hidden; }
.cf-header-text h6 { font-size:.58rem; font-weight:800; line-height:1.2; text-transform:uppercase; letter-spacing:.4px; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.cf-header-text p  { font-size:.46rem; color:rgba(255,255,255,.75); margin:0; }
.cf-header-qr canvas, .cf-header-qr img { border-radius:3px; display:block; }

.cf-body { flex:1; display:flex; padding:9px 11px; gap:10px; align-items:center; }

.cf-photo {
    width:66px; height:78px;
    border-radius:8px;
    border:2.5px solid #B1083C;
    overflow:hidden;
    background:#f3f4f6;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
}
.cf-photo img { width:100%; height:100%; object-fit:cover; }
.cf-photo .ph { font-size:2rem; color:#d1d5db; }

.cf-info { flex:1; min-width:0; }
.cf-name  { font-size:.82rem; font-weight:800; color:#1a1a2e; line-height:1.2; margin-bottom:4px; }
.cf-role-badge {
    display:inline-block;
    background:linear-gradient(90deg,#B1083C,#d13729);
    color:#fff; font-size:.56rem; font-weight:700;
    padding:2px 8px; border-radius:20px;
    text-transform:uppercase; letter-spacing:.4px;
    margin-bottom:6px;
    max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
}
.cf-validity   { font-size:.57rem; color:#6b7280; display:flex; align-items:center; gap:3px; margin-bottom:3px; }
.cf-validity strong { color:#1a1a2e; }
.cf-clinic-lbl { font-size:.52rem; color:#B1083C; font-weight:600; }
.cf-emp-id     { font-size:.5rem; color:#9ca3af; margin-top:3px; }

.cf-footer {
    background:#1a1a2e;
    padding:5px 11px;
    display:flex; align-items:center; justify-content:space-between;
    flex-shrink:0;
}
.cf-footer-label { font-size:.5rem; color:rgba(255,255,255,.4); letter-spacing:.4px; text-transform:uppercase; }
.cf-stripes { display:flex; gap:3px; }
.cf-stripes span { width:13px; height:4px; border-radius:2px; display:block; }

/* BACK */
.card-back {
    background:linear-gradient(160deg,#1a1a2e 0%,#2d2d4e 100%);
    display:flex; flex-direction:column; align-items:center;
    padding:12px 14px;
    position:relative;
    overflow:hidden;
}
.cb-accent-top    { position:absolute; top:0; left:0; right:0; height:5px; background:linear-gradient(90deg,#B1083C,#d13729); }
.cb-accent-bottom { position:absolute; bottom:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#B1083C,#d13729); }
.cb-watermark {
    position:absolute; font-size:6rem; font-weight:900;
    color:rgba(255,255,255,.04);
    top:50%; left:50%;
    transform:translate(-50%,-50%) rotate(-25deg);
    white-space:nowrap; pointer-events:none; user-select:none;
}
.cb-scan-label { font-size:.52rem; font-weight:700; color:rgba(255,255,255,.4); letter-spacing:1.5px; text-transform:uppercase; margin-top:6px; margin-bottom:6px; }
.cb-qr {
    background:#fff; border-radius:10px; padding:6px;
    box-shadow:0 2px 10px rgba(0,0,0,.35); line-height:0;
}
.cb-qr canvas, .cb-qr img { display:block; }
.cb-info { text-align:center; margin-top:7px; }
.cb-clinic { font-size:.58rem; font-weight:800; color:#fff; letter-spacing:.3px; text-transform:uppercase; margin-bottom:2px; }
.cb-address { font-size:.5rem; color:rgba(255,255,255,.6); line-height:1.5; margin-bottom:2px; }
.cb-phone   { font-size:.56rem; color:#B1083C; font-weight:700; }

/* ─── Print overlay (injected by JS) ─────────────────────────────── */
#printOverlay {
    display:none;
    position:fixed; inset:0; z-index:99999;
    background:#fff; overflow:auto; padding:10mm;
}
#printOverlay .po-grid {
    display:flex; flex-wrap:wrap; gap:8mm;
    justify-content:flex-start;
}
#printOverlay .po-set {
    display:flex; flex-direction:column; gap:4mm;
    page-break-inside:avoid;
}
#printOverlay .po-label { display:none; }
#printOverlay .id-card { box-shadow:none !important; border:1px solid #e0e0e0; }

@media print {
    body > *:not(#printOverlay) { display:none !important; }
    #printOverlay {
        display:block !important;
        position:static !important;
        padding:0 !important;
    }
    #printOverlay .po-grid { gap:6mm; }
    #printOverlay .po-set { page-break-inside:avoid; }
    /* Ensure gradients and background colors print */
    * { -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; }
}
</style>

{{-- Print overlay container (outside the admin layout flow) --}}
<div id="printOverlay">
    <div class="po-grid" id="poGrid"></div>
</div>

<script>
const CLINIC_FULLADDR = 'Super Market, F-6 Markaz, Islamabad';
const CLINIC_PHONE    = '0333-5560507';
const VALIDITY        = '31 Dec 2028';

let allUsers     = [];   // full list from AJAX
let selectedIds  = new Set();
let clinicName   = '';

// ── Step 1: enable role select when clinic chosen ─────────────────────
document.getElementById('sel-clinic').addEventListener('change', function () {
    const hasClinic = !!this.value;
    const opt = this.options[this.selectedIndex];
    clinicName = opt.dataset.name || '';

    document.getElementById('sel-role').disabled = !hasClinic;
    if (!hasClinic) {
        document.getElementById('sel-role').value = '';
        document.getElementById('fetchBtn').disabled = true;
    }
    checkFetchReady();
    resetUserSection();
});

document.getElementById('sel-role').addEventListener('change', function () {
    checkFetchReady();
    resetUserSection();
});

function checkFetchReady() {
    const clinic = document.getElementById('sel-clinic').value;
    const role   = document.getElementById('sel-role').value;
    document.getElementById('fetchBtn').disabled = !(clinic && role);
}

function resetUserSection() {
    selectedIds.clear();
    allUsers = [];
    document.getElementById('userSelectCard').style.display = 'none';
    document.getElementById('cardsOutput').style.display    = 'none';
    document.getElementById('previewEmpty').style.display   = 'block';
    document.getElementById('printBtn').classList.add('d-none');
}

// ── Fetch users via AJAX ──────────────────────────────────────────────
function fetchUsers() {
    const clinic = document.getElementById('sel-clinic').value;
    const role   = document.getElementById('sel-role').value;
    if (!clinic || !role) return;

    const btn = document.getElementById('fetchBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Loading…';
    btn.disabled = true;

    $.getJSON('{{ route("staff-id-cards.users") }}', { clinic_id: clinic, role: role })
        .done(function (data) {
            allUsers = data;
            selectedIds.clear();
            renderUserList(data);
            document.getElementById('userSelectCard').style.display = 'block';
        })
        .fail(function () {
            Swal.fire('Error', 'Could not load staff. Please try again.', 'error');
        })
        .always(function () {
            btn.innerHTML = '<i class="bi bi-search me-1"></i>Load Staff';
            btn.disabled = false;
        });
}

// ── Render user rows ──────────────────────────────────────────────────
function renderUserList(users, filter) {
    const list = document.getElementById('userList');
    const noMsg = document.getElementById('noUsersMsg');
    const genWrap = document.getElementById('generateWrap');

    const filtered = filter
        ? users.filter(u => u.name.toLowerCase().includes(filter.toLowerCase()))
        : users;

    if (filtered.length === 0) {
        list.innerHTML = '';
        noMsg.classList.remove('d-none');
        genWrap.style.display = 'none';
        updateCount();
        return;
    }
    noMsg.classList.add('d-none');

    list.innerHTML = filtered.map(u => `
        <div class="usr-row ${selectedIds.has(u.id) ? 'selected' : ''}" onclick="toggleUser(${u.id})" id="usrrow-${u.id}">
            <div class="usr-check"></div>
            ${u.avatar
                ? `<img src="${u.avatar}" class="usr-avatar" alt="${u.name}" onerror="this.parentNode.querySelector('.usr-avatar-ph')?.remove(); this.replaceWith(makePh())">`
                : `<div class="usr-avatar-ph">👤</div>`
            }
            <div class="usr-info">
                <strong>${u.name}</strong>
                <small>${u.emp_id} &nbsp;·&nbsp; ${u.role}</small>
            </div>
        </div>
    `).join('');

    genWrap.style.display = selectedIds.size > 0 ? 'block' : 'none';
    updateCount();
}

function toggleUser(id) {
    if (selectedIds.has(id)) {
        selectedIds.delete(id);
    } else {
        selectedIds.add(id);
    }
    const filter = document.getElementById('userSearch').value;
    renderUserList(allUsers, filter || null);
}

function selectAll() {
    allUsers.forEach(u => selectedIds.add(u.id));
    const filter = document.getElementById('userSearch').value;
    renderUserList(allUsers, filter || null);
}

function selectNone() {
    selectedIds.clear();
    const filter = document.getElementById('userSearch').value;
    renderUserList(allUsers, filter || null);
}

function updateCount() {
    document.getElementById('selCount').textContent = selectedIds.size + ' selected';
    document.getElementById('generateWrap').style.display = selectedIds.size > 0 ? 'block' : 'none';
    document.getElementById('generateBtn').innerHTML =
        `<i class="bi bi-credit-card me-1"></i>Generate ${selectedIds.size} Card${selectedIds.size !== 1 ? 's' : ''}`;
}

document.getElementById('userSearch').addEventListener('input', function () {
    renderUserList(allUsers, this.value || null);
});

// ── Generate Cards ────────────────────────────────────────────────────
function generateCards() {
    const selected = allUsers.filter(u => selectedIds.has(u.id));
    if (!selected.length) return;

    const grid = document.getElementById('cardsGrid');
    grid.innerHTML = '';

    selected.forEach(staff => {
        const set = document.createElement('div');
        set.className = 'id-card-set';

        const fWrap = document.createElement('div');
        fWrap.innerHTML = `<div class="id-card-label">${staff.name} — Front</div>`;
        fWrap.appendChild(buildFront(staff));
        set.appendChild(fWrap);

        const bWrap = document.createElement('div');
        bWrap.innerHTML = `<div class="id-card-label">Back</div>`;
        bWrap.appendChild(buildBack(staff));
        set.appendChild(bWrap);

        grid.appendChild(set);

        setTimeout(() => {
            makeQR('qr-f-' + staff.id, qrText(staff), 40);
            makeQR('qr-b-' + staff.id, qrText(staff), 92);
        }, 60);
    });

    document.getElementById('previewEmpty').style.display = 'none';
    document.getElementById('cardsOutput').style.display  = 'block';
    document.getElementById('printBtn').classList.remove('d-none');
    document.getElementById('cardsOutput').scrollIntoView({ behavior:'smooth', block:'start' });
}

function qrText(s) {
    return `${clinicName || 'D.M.D Clinic'} | ${s.name} | ${s.role} | ID: ${s.emp_id} | Valid: ${VALIDITY}`;
}

function makeQR(elId, text, size) {
    const el = document.getElementById(elId);
    if (!el) return;
    el.innerHTML = '';
    try { new QRCode(el, { text, width:size, height:size, colorDark:'#1a1a2e', colorLight:'#ffffff', correctLevel:QRCode.CorrectLevel.M }); }
    catch(e) {}
}

// ── Build Front ───────────────────────────────────────────────────────
function buildFront(s) {
    const card = document.createElement('div');
    card.className = 'id-card card-front';

    const displayClinic = clinicName || s.clinic || 'D.M.D Clinic';

    card.innerHTML = `
        <div class="cf-header">
            <div class="cf-logo">DMD</div>
            <div class="cf-header-text">
                <h6>${displayClinic}</h6>
                <p>D.M.D Aesthetic, Dental &amp; Hair Transplant</p>
            </div>
            <div class="cf-header-qr">
                <div id="qr-f-${s.id}"></div>
            </div>
        </div>
        <div class="cf-body">
            <div class="cf-photo">
                ${s.avatar
                    ? `<img src="${s.avatar}" alt="${s.name}" onerror="this.outerHTML='<div class=\\'ph\\'>👤</div>'">`
                    : `<div class="ph">👤</div>`
                }
            </div>
            <div class="cf-info">
                <div class="cf-name">${s.name}</div>
                <div class="cf-role-badge">${s.role}</div>
                <div class="cf-validity">
                    <i class="bi bi-calendar-check" style="font-size:.55rem"></i>
                    Valid Until: <strong>${VALIDITY}</strong>
                </div>
                <div class="cf-clinic-lbl"><i class="bi bi-hospital me-1" style="font-size:.5rem"></i>${displayClinic}</div>
                <div class="cf-emp-id">ID: ${s.emp_id}</div>
            </div>
        </div>
        <div class="cf-footer">
            <span class="cf-footer-label">Staff Identity Card</span>
            <div class="cf-stripes">
                <span style="background:#B1083C"></span>
                <span style="background:#d13729"></span>
                <span style="background:#f59e0b"></span>
                <span style="background:rgba(255,255,255,.25)"></span>
            </div>
        </div>
    `;
    return card;
}

// ── Build Back ────────────────────────────────────────────────────────
function buildBack(s) {
    const card = document.createElement('div');
    card.className = 'id-card card-back';
    const displayClinic = clinicName || s.clinic || 'D.M.D Clinic';
    card.innerHTML = `
        <div class="cb-accent-top"></div>
        <div class="cb-watermark">DMD</div>
        <div class="cb-scan-label">Scan to Verify</div>
        <div class="cb-qr"><div id="qr-b-${s.id}"></div></div>
        <div class="cb-info">
            <div class="cb-clinic">${displayClinic}</div>
            <div class="cb-address">${CLINIC_FULLADDR}</div>
            <div class="cb-phone">${CLINIC_PHONE}</div>
        </div>
        <div class="cb-accent-bottom"></div>
    `;
    return card;
}

// ── Print: clone cards into overlay so admin chrome disappears ────────
function triggerPrint() {
    const sourceGrid = document.getElementById('cardsGrid');
    const poGrid     = document.getElementById('poGrid');
    const overlay    = document.getElementById('printOverlay');

    // Clone each card-set into the print overlay (deep clone keeps QR canvases)
    poGrid.innerHTML = '';
    Array.from(sourceGrid.children).forEach(set => {
        const clone = set.cloneNode(true);
        clone.className = 'po-set';
        poGrid.appendChild(clone);
    });

    overlay.style.display = 'block';
    document.body.style.overflow = 'hidden';

    setTimeout(() => {
        window.print();
    }, 120); // tiny delay so overlay renders before print dialog opens
}

// Restore normal view after print dialog closes
window.addEventListener('afterprint', function () {
    document.getElementById('printOverlay').style.display = 'none';
    document.body.style.overflow = '';
});
</script>
@endsection
