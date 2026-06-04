@extends('layouts.admin')
@section('title', isset($pos) ? 'Edit Order #' . $pos->order_number : 'New POS Sale')

@section('content')
<style>
    /* ── FIX 1 & 6: Full-page — hide sidenav, expand content ─── */
    .left-menu          { display: none !important; }
    .content-wrapper    { margin-left: 0 !important; width: 100% !important; }
    .min-height-css     { padding: 12px !important; }

    /* ── Page header bar ─────────────────────────────────────── */
    .pos-header {
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        background: #fff; border-radius: 8px; padding: 10px 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,.07); margin-bottom: 12px;
    }

    /* ── Cart panel (LEFT) ────────────────────────────────────── */
    .pos-cart-sticky {
        position: sticky; top: 68px;
        max-height: calc(100vh - 82px);
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #d1d5db transparent;
        scrollbar-gutter: stable;
    }
    .pos-cart-sticky::-webkit-scrollbar { width: 4px; }
    .pos-cart-sticky::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

    /* Cart items list — scrollable when multiple products */
    .cart-items {
        max-height: calc(100vh - 560px);
        overflow-y: auto;
        scrollbar-width: thin; scrollbar-color: transparent transparent;
    }
    .cart-items:hover { scrollbar-color: #d1d5db transparent; }
    .cart-items::-webkit-scrollbar { width: 4px; }
    .cart-items::-webkit-scrollbar-thumb { background: transparent; border-radius: 4px; }
    .cart-items:hover::-webkit-scrollbar-thumb { background: #d1d5db; }

    .cart-row { display:flex; align-items:center; gap:5px; padding:5px 0; border-bottom:1px solid #f0f0f0; font-size:.82rem; }
    .cart-row .cart-name  { flex:1; min-width:0; }
    .cart-row .cart-qty   { width:52px; }
    .cart-row .cart-price { width:68px; }
    .cart-row .cart-total { width:60px; text-align:right; font-weight:600; color:#B1083C; white-space:nowrap; }
    .cart-row .cart-del   { flex-shrink:0; }

    .cart-summary { border-top:2px solid #f0f0f0; padding-top:8px; font-size:.85rem; }
    .cart-summary .row-line { display:flex; justify-content:space-between; align-items:center; margin-bottom:5px; }
    .cart-summary .grand   { font-size:1rem; font-weight:700; color:#B1083C; border-top:1px solid #ddd; padding-top:6px; margin-top:4px; }

    /* ── Product panel (RIGHT) ────────────────────────────────── */
    .pos-products-panel {
        background: #fff; border-radius: 8px;
        box-shadow: 0 1px 6px rgba(0,0,0,.07);
        padding: 14px; display: flex; flex-direction: column;
        height: calc(100vh - 110px);
    }
    .pos-search-bar { display:flex; gap:8px; margin-bottom:12px; flex-wrap:wrap; flex-shrink:0; }
    .pos-search-bar input  { flex:1; min-width:140px; }
    .pos-search-bar select { width:180px; flex-shrink:0; }

    /* Product grid */
    .prod-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(145px, 1fr));
        gap: 10px; flex: 1; overflow-y: auto; padding-right: 2px;
        align-content: start;
    }
    .prod-grid { scrollbar-width: thin; scrollbar-color: transparent transparent; }
    .prod-grid:hover { scrollbar-color: #d1d5db transparent; }
    .prod-grid::-webkit-scrollbar { width: 4px; }
    .prod-grid::-webkit-scrollbar-thumb { background: transparent; border-radius: 4px; }
    .prod-grid:hover::-webkit-scrollbar-thumb { background: #d1d5db; }

    /* Product cards — no transform/translate to prevent stacking-context overlap with sidenav */
    .prod-card {
        border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 10px;
        cursor: pointer; background: #fff;
        text-align: center; user-select: none; height: fit-content;
    }
    .prod-card:hover { border-color:#B1083C; box-shadow:0 2px 8px rgba(177,8,60,.15); background:#fff5f7; }
    .prod-card .prod-name  { font-weight:600; font-size:.82rem; color:#1f2937; line-height:1.3; }
    .prod-card .prod-price { font-size:.8rem; color:#B1083C; font-weight:700; margin-top:4px; }
    .prod-card .prod-stock { font-size:.72rem; color:#6b7280; margin-top:2px; }
    .prod-card .stock-low  { color:#f59e0b; }
    .prod-card .stock-out  { color:#ef4444; }

    /* Variation modal */
    .var-btn { cursor:pointer; border:1px solid #e2e8f0; border-radius:6px; padding:8px 12px; }
    .var-btn:hover, .var-btn.selected { border-color:#B1083C; background:#fff5f7; }

    /* Order type toggle */
    .order-type-btn {
        border:2px solid #e2e8f0; border-radius:8px; padding:7px 8px;
        cursor:pointer; text-align:center; flex:1; background:#fff;
    }
    .order-type-btn.active { border-color:#B1083C; background:#fff5f7; color:#B1083C; }
    .order-type-btn:hover:not(.active) { border-color:#d1d5db; background:#f9fafb; }

    /* Load order bar */
    .load-order-bar { background:#f8f9fa; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px; }

    /* ── Select2 ─ Bootstrap-5 compatibility reset ───────────────── */
    .select2-container--default .select2-selection--single {
        height: auto !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.25rem;
        padding: 0.25rem 2rem 0.25rem 0.5rem;
        line-height: 1.5;
        background-color: #fff;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding: 0 !important;
        color: #212529 !important;
        line-height: 1.5;
        font-size: .875rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        top: 0 !important;
        right: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__clear {
        font-size: 1rem; line-height: 1.2; margin-right: 6px; color: #6c757d;
    }
    .select2-dropdown {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        font-size: .875rem;
        z-index: 9999;
    }
    .select2-results__option { padding: 0.35rem 0.5rem; }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #dee2e6;
        border-radius: 0.2rem;
        padding: 0.25rem 0.4rem;
        font-size: .875rem;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .pos-cart-sticky { position:static; max-height:none; overflow-y:visible; }
        .pos-products-panel { height:auto; }
        .prod-grid { max-height:50vh; }
        .cart-items { max-height:160px; }
    }
    @media (max-width: 575.98px) {
        .pos-search-bar select { width:100%; }
        .prod-grid { grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); }
        .cart-row .cart-price { display:none; }
    }
</style>

{{-- ── Page Header ──────────────────────────────────────────────── --}}
<div class="pos-header">
    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-house-fill me-1"></i>Home
    </a>
    <span class="fw-bold text-danger" style="font-size:.95rem">
        <i class="bi bi-cash-stack me-1"></i>
        {{ isset($pos) ? 'Edit Order — #' . $pos->order_number : 'Point of Sale' }}
    </span>
    <a href="{{ route('pos.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
        <i class="bi bi-arrow-left me-1"></i>Back to Orders
    </a>
</div>

<div class="row g-3">

    {{-- ═══════════════════════════════════════════════════════════
         FIX 4: Cart panel is now on the LEFT
    ════════════════════════════════════════════════════════════ --}}
    <div class="col-12 col-lg-4 col-xl-4">
    <div class="pos-cart-sticky">
    <div class="card shadow-sm h-100">
        <div class="card-header py-2 text-white d-flex align-items-center justify-content-between"
             style="background:linear-gradient(90deg,#B1083C,#d13729)">
            <span>
                <i class="bi bi-cart3 me-1"></i>
                <strong>{{ isset($pos) ? 'Edit Order' : 'Cart' }}</strong>
                <span class="badge bg-white text-danger ms-2" id="cartCount">0</span>
            </span>
            @isset($pos)
            <span class="badge bg-white text-danger" style="font-size:.7rem">{{ $pos->order_number }}</span>
            @endisset
        </div>
        <div class="card-body d-flex flex-column p-2" style="gap:6px;">

            {{-- ── Row: Load Order (left) + Clinic (right) ── --}}
            <div class="row g-2">

                {{-- Load Order (create mode only) --}}
                @unless(isset($pos))
                <div class="{{ auth()->user()->isSuperAdmin() ? 'col-6' : 'col-12' }}">
                    <div class="load-order-bar h-100">
                        <label class="form-label small fw-semibold mb-1">
                            <i class="bi bi-search me-1 text-danger"></i>Load Order
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="loadOrderInput" class="form-control"
                                   placeholder="POS-XXXXXX-XXXX" autocomplete="off">
                            <button class="btn btn-outline-danger px-2" type="button" id="btnLoadOrder">
                                <i class="bi bi-arrow-right-circle"></i>
                            </button>
                        </div>
                        <div id="loadOrderMsg" class="small mt-1 d-none"></div>
                    </div>
                </div>
                @endunless

                {{-- Clinic --}}
                @if(auth()->user()->isSuperAdmin())
                <div class="{{ isset($pos) ? 'col-12' : 'col-6' }}">
                    <label class="form-label small fw-semibold mb-1"><i class="bi bi-building me-1"></i>Clinic</label>
                    <select id="selectClinic" class="form-select form-select-sm w-100">
                        <option value="">— Select —</option>
                        @foreach($clinics as $c)
                        <option value="{{ $c->id }}" {{ isset($pos) && $pos->clinic_id == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" id="selectClinic" value="{{ $myClinic?->id ?? '' }}">
                <div class="col-12">
                    <div class="small text-muted py-1 px-2 rounded" style="background:#f8f9fa;border:1px solid #e2e8f0">
                        <i class="bi bi-building me-1 text-danger"></i>
                        <strong>{{ $myClinic?->name ?? 'No clinic assigned' }}</strong>
                    </div>
                </div>
                @endif

            </div>{{-- /row --}}

            {{-- FIX 2 & 3: #cartEmpty is a SIBLING of #cartItems, NOT a child.
                 This prevents container.innerHTML = '' from destroying the element reference. --}}
            <div id="cartEmpty" class="text-center text-muted py-3 small">
                <i class="bi bi-cart-x d-block fs-3 mb-1"></i>No items added yet
            </div>
            <div class="cart-items" id="cartItems"></div>

            {{-- Totals --}}
            <div class="cart-summary">
                <div class="row-line"><span>Subtotal</span><span id="sumSubtotal">0.00</span></div>
                <div class="row-line">
                    <span>Discount</span>
                    <input type="number" id="inputDiscount"
                           class="form-control form-control-sm text-end" style="width:88px"
                           min="0" step="0.01" value="{{ isset($pos) ? $pos->discount : 0 }}" placeholder="0.00">
                </div>
                <div class="row-line align-items-center">
                    <span>Tax</span>
                    <div class="d-flex gap-1">
                        <input type="text"   id="inputTaxLabel" class="form-control form-control-sm" style="width:50px" placeholder="GST" value="{{ isset($pos) ? $pos->tax_label : '' }}">
                        <input type="number" id="inputTaxRate"  class="form-control form-control-sm text-end" style="width:54px" min="0" max="100" step="0.01" value="{{ isset($pos) ? $pos->tax_rate : 0 }}" placeholder="%">
                    </div>
                </div>
                <div class="row-line text-muted small"><span>Tax Amt</span><span id="sumTax">0.00</span></div>
                <div class="row-line grand"><span>Grand Total</span><span id="sumGrand">0.00</span></div>
            </div>

            <hr class="my-1">

            {{-- Patient --}}
            <div>
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <label class="form-label small fw-semibold mb-0">Patient <span class="text-danger">*</span></label>
                    <button type="button" class="btn btn-sm py-0 px-2 text-white"
                            style="font-size:.72rem;background:#B1083C;border:none"
                            data-bs-toggle="modal" data-bs-target="#addPatientModal">
                        <i class="bi bi-person-plus me-1"></i>New
                    </button>
                </div>
                <div class="position-relative">
                    <input type="text" id="patientSearch" class="form-control form-control-sm"
                           placeholder="Name or phone…" autocomplete="off">
                    <div id="patientDropdown"
                         class="position-absolute w-100 bg-white border rounded shadow-sm d-none"
                         style="z-index:1050;max-height:180px;overflow-y:auto;top:100%;left:0;font-size:.82rem"></div>
                </div>
                <input type="hidden" id="selectedPatientId" value="{{ isset($pos) ? $pos->user_id : '' }}">
                <div id="patientSelected"
                     class="{{ isset($pos) && $pos->patient ? '' : 'd-none' }} mt-1 p-1 rounded small"
                     style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0">
                    <i class="bi bi-person-check me-1"></i>
                    <span id="patientSelectedName">{{ isset($pos) ? ($pos->patient?->name ?? '') : '' }}</span>
                    <button type="button" class="btn-close btn-close-sm float-end" id="clearPatient" style="font-size:.5rem"></button>
                </div>
            </div>

            {{-- Order Type --}}
            <div>
                <label class="form-label small fw-semibold mb-1">Order Type</label>
                <div class="d-flex gap-2">
                    <div class="order-type-btn {{ !isset($pos) || $pos->order_type === 'takeaway' ? 'active' : '' }}"
                         id="btnTakeaway" onclick="setOrderType('takeaway')">
                        <i class="bi bi-bag-check d-block fs-5"></i>
                        <div class="small fw-semibold" style="font-size:.72rem!important">Takeaway</div>
                    </div>
                    <div class="order-type-btn {{ isset($pos) && $pos->order_type === 'delivery' ? 'active' : '' }}"
                         id="btnDelivery" onclick="setOrderType('delivery')">
                        <i class="bi bi-truck d-block fs-5"></i>
                        <div class="small fw-semibold" style="font-size:.72rem!important">Delivery</div>
                    </div>
                </div>
            </div>

            {{-- Delivery Address --}}
            <div id="deliverySection" class="{{ isset($pos) && $pos->order_type === 'delivery' ? '' : 'd-none' }}">
                <label class="form-label small fw-semibold mb-1">Delivery Address <span class="text-danger">*</span></label>
                <textarea id="inputDelivery" class="form-control form-control-sm" rows="2"
                          placeholder="Full delivery address…">{{ isset($pos) ? $pos->delivery_address : '' }}</textarea>
            </div>

            {{-- Notes --}}
            <div>
                <label class="form-label small fw-semibold mb-1">Notes</label>
                <input type="text" id="inputNotes" class="form-control form-control-sm"
                       placeholder="Order notes…" value="{{ isset($pos) ? $pos->notes : '' }}">
            </div>

            {{-- Payment Status --}}
            <div>
                <label class="form-label small fw-semibold mb-1">Payment</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentStatus" id="radioUnpaid" value="unpaid"
                               {{ !isset($pos) || $pos->payment_status === 'unpaid' ? 'checked' : '' }}>
                        <label class="form-check-label small" for="radioUnpaid">
                            <span class="badge bg-warning text-dark">Unpaid</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentStatus" id="radioPaid" value="paid"
                               {{ isset($pos) && $pos->payment_status === 'paid' ? 'checked' : '' }}>
                        <label class="form-check-label small" for="radioPaid">
                            <span class="badge bg-success">Paid</span>
                        </label>
                    </div>
                </div>
            </div>

            <div id="checkoutAlert" class="alert alert-danger d-none py-1 small mb-0"></div>
            <button id="btnCheckout" class="btn btn-sm text-white fw-semibold w-100 py-2"
                    style="background:linear-gradient(90deg,#B1083C,#d13729);border:none;">
                <i class="bi bi-bag-check me-1"></i>
                {{ isset($pos) ? 'Update Order' : 'Place Order' }}
            </button>

        </div>{{-- card-body --}}
    </div>{{-- card --}}
    </div>{{-- pos-cart-sticky --}}
    </div>{{-- col (cart) --}}

    {{-- ═══════════════════════════════════════════════════════════
         FIX 4: Products panel is now on the RIGHT
    ════════════════════════════════════════════════════════════ --}}
    <div class="col-12 col-lg-8 col-xl-8">
        <div class="pos-products-panel">
            <div class="pos-search-bar">
                <input type="text" id="prodSearch" class="form-control form-control-sm"
                       placeholder="Search products…">
                {{-- FIX 5: Category filter --}}
                <select id="prodCategory" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="prod-grid" id="prodGrid">
                <div class="text-center text-muted py-5 w-100" style="grid-column:1/-1">
                    <div class="spinner-border spinner-border-sm text-danger"></div>
                    <p class="mt-2 small">Loading products…</p>
                </div>
            </div>
        </div>
    </div>

</div>{{-- row --}}

{{-- Hidden data-api trigger for varModal --}}
<button id="varModalTrigger" class="d-none"
        data-bs-toggle="modal" data-bs-target="#varModal"></button>

{{-- Variation modal --}}
<div class="modal fade" id="varModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2" style="background:linear-gradient(90deg,#B1083C,#d13729)">
                <h6 class="modal-title text-white mb-0" id="varModalTitle">Select Variation</h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="varModalBody"></div>
        </div>
    </div>
</div>

{{-- ══ Add Patient Modal ══════════════════════════════════════════════ --}}
<div class="modal fade" id="addPatientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2" style="background:linear-gradient(90deg,#B1083C,#d13729)">
                <h6 class="modal-title text-white mb-0"><i class="bi bi-person-plus me-1"></i>Add Patient</h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div id="addPatientAlert" class="alert alert-danger d-none py-1 small mb-2"></div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold mb-1">Name <span class="text-danger">*</span></label>
                    <input type="text" id="newPatientName" class="form-control form-control-sm" placeholder="Full name">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold mb-1">Phone</label>
                    <input type="text" id="newPatientPhone" class="form-control form-control-sm" placeholder="03XXXXXXXXX">
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold mb-1">Email</label>
                    <input type="email" id="newPatientEmail" class="form-control form-control-sm" placeholder="optional">
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="btnSavePatient" class="btn btn-sm text-white"
                        style="background:#B1083C;border:none">
                    <i class="bi bi-person-check me-1"></i>Save & Select
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
{{-- SweetAlert2 already loaded by admin layout — DO NOT re-import --}}
<script>
(function () {
    const CSRF     = () => document.querySelector('meta[name="csrf-token"]').content;
    const currency = '{{ auth()->user()->currency ?? "PKR " }}';
    const editingOrderId = {{ isset($pos) ? $pos->id : 'null' }};

    let cart      = [];
    let orderType = '{{ isset($pos) ? $pos->order_type : "takeaway" }}';
    let searchTimer;

    /* ══════════════════════════════════════════════════════════════
       EDIT MODE — pre-fill cart from existing order
    ══════════════════════════════════════════════════════════════ */
    @isset($pos)
    cart = [
        @foreach($pos->items as $item)
        {
            key:           '{{ $item->product_id }}_{{ $item->variation_id ?? "null" }}',
            productId:     {{ $item->product_id }},
            variationId:   {!! json_encode($item->variation_id) !!},
            productName:   {!! json_encode($item->product_name) !!},
            variationName: {!! json_encode($item->variation_name) !!},
            unitPrice:     {{ $item->unit_price }},
            quantity:      {{ $item->quantity }},
            lineTotal:     {{ $item->line_total }},
            stock:         null,
            trackInventory: false,
        },
        @endforeach
    ];
    renderCart();
    @endisset
    @unless(isset($pos))
    renderCart(); // create mode — ensures #cartEmpty shows, #cartItems is empty
    @endunless

    /* ══════════════════════════════════════════════════════════════
       ORDER TYPE
    ══════════════════════════════════════════════════════════════ */
    function setOrderType(type) {
        orderType = type;
        document.getElementById('btnTakeaway').classList.toggle('active', type === 'takeaway');
        document.getElementById('btnDelivery').classList.toggle('active', type === 'delivery');
        document.getElementById('deliverySection').classList.toggle('d-none', type !== 'delivery');
        if (type === 'delivery' && window._patientShippingAddress &&
            !document.getElementById('inputDelivery').value.trim()) {
            document.getElementById('inputDelivery').value = window._patientShippingAddress;
        }
    }
    window.setOrderType = setOrderType;

    /* ══════════════════════════════════════════════════════════════
       LOAD EXISTING ORDER (create mode only)
    ══════════════════════════════════════════════════════════════ */
    @unless(isset($pos))
    document.getElementById('btnLoadOrder').addEventListener('click', loadExistingOrder);
    document.getElementById('loadOrderInput').addEventListener('keydown', e => {
        if (e.key === 'Enter') loadExistingOrder();
    });
    function loadExistingOrder() {
        const orderNum = document.getElementById('loadOrderInput').value.trim();
        if (!orderNum) return;
        const msgEl = document.getElementById('loadOrderMsg');
        msgEl.className = 'small mt-1 text-muted';
        msgEl.textContent = 'Searching…';
        msgEl.classList.remove('d-none');
        fetch('/pos/load-order?' + new URLSearchParams({ order_number: orderNum }), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data || data.error) {
                msgEl.className = 'small mt-1 text-danger';
                msgEl.textContent = data?.error || 'Order not found.';
                return;
            }
            msgEl.className = 'small mt-1 text-success';
            msgEl.textContent = 'Found! Opening edit mode…';
            setTimeout(() => { window.location.href = '/pos/' + data.id + '/edit'; }, 500);
        })
        .catch(() => { msgEl.className = 'small mt-1 text-danger'; msgEl.textContent = 'Request failed.'; });
    }
    @endunless

    /* ══════════════════════════════════════════════════════════════
       FIX 5: PRODUCT BROWSER — only pass category_id when non-empty
    ══════════════════════════════════════════════════════════════ */
    function loadProducts() {
        const q    = document.getElementById('prodSearch').value.trim();
        const cat  = document.getElementById('prodCategory').value;
        const grid = document.getElementById('prodGrid');

        grid.innerHTML = '<div class="text-center text-muted py-4 w-100" style="grid-column:1/-1">'
            + '<div class="spinner-border spinner-border-sm text-danger"></div>'
            + '<p class="mt-2 small">Loading…</p></div>';

        // FIX 5: Only include category_id param when a category is actually selected
        const params = new URLSearchParams({ search: q });
        if (cat) params.set('category_id', cat);

        fetch('/pos/products/search?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(products => {
            if (!products.length) {
                const catName = document.getElementById('prodCategory').selectedOptions[0]?.text || '';
                grid.innerHTML = `<div class="text-center text-muted py-5 w-100" style="grid-column:1/-1">
                    <i class="bi bi-inbox d-block fs-2 mb-2"></i>
                    ${cat ? 'No products in <strong>' + esc(catName) + '</strong>.' : 'No products found.'}
                </div>`;
                return;
            }
            grid.innerHTML = products.map(p => {
                const stockHtml = p.has_variations
                    ? '<div class="prod-stock">Has variations</div>'
                    : stockBadge(p.stock);
                return `<div class="prod-card" data-product='${JSON.stringify(p).replace(/'/g, "&#39;")}'>
                    <div class="prod-name">${esc(p.name)}</div>
                    <div class="prod-price">${currency}${parseFloat(p.price).toFixed(2)}</div>
                    ${stockHtml}
                </div>`;
            }).join('');
        })
        .catch(() => {
            grid.innerHTML = '<div class="text-center text-danger py-5 w-100" style="grid-column:1/-1">'
                + '<i class="bi bi-exclamation-triangle d-block fs-2 mb-2"></i>Failed to load products.</div>';
        });
    }

    function stockBadge(qty) {
        if (qty === null || qty === undefined) return '';
        if (qty <= 0)  return '<div class="prod-stock stock-out">Out of stock</div>';
        if (qty < 10)  return `<div class="prod-stock stock-low">Low: ${qty}</div>`;
        return `<div class="prod-stock">Stock: ${qty}</div>`;
    }

    loadProducts();
    document.getElementById('prodSearch').addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadProducts, 350);
    });
    // Select2 fires jQuery change events only — must use $.on(), not addEventListener
    $('#prodCategory').on('change', loadProducts);

    /* ── Click product card ─────────────────────────────────── */
    document.getElementById('prodGrid').addEventListener('click', function (e) {
        const card = e.target.closest('.prod-card');
        if (!card) return;
        const product = JSON.parse(card.dataset.product.replace(/&#39;/g, "'"));
        if (product.has_variations && product.variations?.length) {
            openVarModal(product);
        } else {
            addToCart(product.id, null, product.name, null,
                      product.price, product.stock, product.track_inventory);
        }
    });

    /* ══════════════════════════════════════════════════════════════
       VARIATION MODAL
    ══════════════════════════════════════════════════════════════ */
    let varPending = null;

    function openVarModal(product) {
        varPending = null;
        document.getElementById('varModalTitle').textContent = product.name;
        const body = document.getElementById('varModalBody');
        body.innerHTML = product.variations.map(v => {
            const disabled = (v.stock !== null && v.stock <= 0) ? 'opacity-50 pe-none' : '';
            return `<div class="var-btn mb-2 ${disabled}"
                    data-vid="${v.id}" data-pid="${product.id}"
                    data-pname="${esc(product.name)}" data-vname="${esc(v.name)}"
                    data-price="${v.price}" data-stock="${v.stock}"
                    data-track="${product.track_inventory}">
                <div class="fw-semibold small">${esc(v.name)}</div>
                <div class="small text-muted">${currency}${parseFloat(v.price).toFixed(2)} ${stockBadge(v.stock)}</div>
            </div>`;
        }).join('');
        body.querySelectorAll('.var-btn:not(.pe-none)').forEach(btn => {
            btn.addEventListener('click', function () {
                varPending = {
                    productId:     parseInt(this.dataset.pid),
                    variationId:   parseInt(this.dataset.vid),
                    productName:   this.dataset.pname,
                    variationName: this.dataset.vname,
                    price:         parseFloat(this.dataset.price),
                    stock:         parseInt(this.dataset.stock),
                    track:         this.dataset.track === '1' || this.dataset.track === 'true',
                };
                document.querySelector('#varModal .btn-close').click();
            });
        });
        document.getElementById('varModalTrigger').click();
    }

    document.getElementById('varModal').addEventListener('hidden.bs.modal', function () {
        if (!varPending) return;
        const v = varPending; varPending = null;
        addToCart(v.productId, v.variationId, v.productName, v.variationName,
                  v.price, v.stock, v.track);
    });

    /* ══════════════════════════════════════════════════════════════
       FIX 2 & 3: CART LOGIC
       #cartEmpty is a sibling of #cartItems — innerHTML never destroys it.
       Clicking same product increments qty.
    ══════════════════════════════════════════════════════════════ */
    function cartKey(productId, variationId) {
        return productId + '_' + (variationId ?? 'null');
    }

    function addToCart(productId, variationId, productName, variationName,
                       price, stock, trackInventory) {
        const key      = cartKey(productId, variationId);
        const existing = cart.find(i => i.key === key);

        if (existing) {
            // FIX 3: Same product → increment quantity
            if (trackInventory && stock !== null && existing.quantity >= stock) {
                Swal.fire({ icon:'warning', title:'Stock Limit',
                    text:'No more stock available.', confirmButtonColor:'#B1083C' });
                return;
            }
            existing.quantity++;
            existing.lineTotal = +(existing.quantity * existing.unitPrice).toFixed(2);
        } else {
            if (trackInventory && stock !== null && stock <= 0) {
                Swal.fire({ icon:'warning', title:'Out of Stock',
                    text:'This product is out of stock.', confirmButtonColor:'#B1083C' });
                return;
            }
            cart.push({
                key, productId, variationId, productName, variationName,
                unitPrice:      +parseFloat(price).toFixed(2),
                quantity:       1,
                lineTotal:      +parseFloat(price).toFixed(2),
                stock, trackInventory,
            });
        }
        renderCart();
    }

    /* FIX 2: renderCart — #cartEmpty is a sibling, never touched by innerHTML */
    function renderCart() {
        const container = document.getElementById('cartItems');
        const empty     = document.getElementById('cartEmpty');
        const count     = document.getElementById('cartCount');

        count.textContent = cart.reduce((s, i) => s + i.quantity, 0);

        // Toggle empty state
        empty.classList.toggle('d-none', cart.length > 0);

        if (!cart.length) {
            container.innerHTML = '';
            updateTotals();
            return;
        }

        // Render rows — #cartEmpty is not inside container, so this is safe
        container.innerHTML = cart.map((item, idx) => `
            <div class="cart-row">
                <div class="cart-name">
                    <div class="fw-semibold" style="font-size:.8rem">${esc(item.productName)}</div>
                    ${item.variationName ? `<div class="text-muted" style="font-size:.72rem">${esc(item.variationName)}</div>` : ''}
                </div>
                <input type="number" class="form-control form-control-sm cart-qty"
                       value="${item.quantity}" min="1"
                       ${item.trackInventory && item.stock !== null ? `max="${item.stock}"` : ''}
                       data-idx="${idx}">
                <input type="number" class="form-control form-control-sm cart-price"
                       value="${item.unitPrice}" min="0" step="0.01" data-idx="${idx}">
                <div class="cart-total">${item.lineTotal.toFixed(2)}</div>
                <button class="btn btn-sm btn-outline-danger py-0 px-1 cart-del" data-idx="${idx}">
                    <i class="bi bi-x"></i>
                </button>
            </div>`).join('');

        updateTotals();
    }

    document.getElementById('cartItems').addEventListener('input', function (e) {
        const idx = +e.target.dataset.idx;
        if (isNaN(idx)) return;
        const item = cart[idx];
        if (!item) return;
        if (e.target.classList.contains('cart-qty')) {
            const qty = Math.max(1, parseInt(e.target.value) || 1);
            if (item.trackInventory && item.stock !== null && qty > item.stock) {
                e.target.value = item.stock; item.quantity = item.stock;
            } else { item.quantity = qty; }
        }
        if (e.target.classList.contains('cart-price')) {
            item.unitPrice = parseFloat(e.target.value) || 0;
        }
        item.lineTotal = +(item.quantity * item.unitPrice).toFixed(2);
        const totals = document.querySelectorAll('.cart-total');
        if (totals[idx]) totals[idx].textContent = item.lineTotal.toFixed(2);
        updateTotals();
    });

    document.getElementById('cartItems').addEventListener('click', function (e) {
        const delBtn = e.target.closest('.cart-del');
        if (!delBtn) return;
        cart.splice(parseInt(delBtn.dataset.idx), 1);
        renderCart();
    });

    /* ══════════════════════════════════════════════════════════════
       TOTALS
    ══════════════════════════════════════════════════════════════ */
    function updateTotals() {
        const subtotal  = cart.reduce((s, i) => s + i.lineTotal, 0);
        const discount  = parseFloat(document.getElementById('inputDiscount').value) || 0;
        const taxRate   = parseFloat(document.getElementById('inputTaxRate').value)  || 0;
        const afterDisc = Math.max(0, subtotal - discount);
        const taxAmount = +(afterDisc * taxRate / 100).toFixed(2);
        const grand     = +(afterDisc + taxAmount).toFixed(2);
        document.getElementById('sumSubtotal').textContent = subtotal.toFixed(2);
        document.getElementById('sumTax').textContent      = taxAmount.toFixed(2);
        document.getElementById('sumGrand').textContent    = grand.toFixed(2);
    }
    ['inputDiscount','inputTaxRate'].forEach(id =>
        document.getElementById(id).addEventListener('input', updateTotals)
    );

    /* ══════════════════════════════════════════════════════════════
       PATIENT SEARCH
    ══════════════════════════════════════════════════════════════ */
    let patientTimer;
    document.getElementById('patientSearch').addEventListener('input', function () {
        clearTimeout(patientTimer);
        const q = this.value.trim();
        if (q.length < 2) { document.getElementById('patientDropdown').classList.add('d-none'); return; }
        patientTimer = setTimeout(() => {
            fetch('/pos/patients/search?' + new URLSearchParams({ q }), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(patients => {
                const dd = document.getElementById('patientDropdown');
                if (!patients.length) { dd.classList.add('d-none'); return; }
                dd.innerHTML = patients.map(p =>
                    `<div class="p-2 border-bottom patient-opt" style="cursor:pointer"
                         data-id="${p.id}" data-name="${esc(p.name)}" data-shipping="">
                        <strong>${esc(p.name)}</strong>
                        <span class="text-muted ms-1 small">${esc(p.phone ?? '')}</span>
                    </div>`
                ).join('');
                dd.classList.remove('d-none');
            });
        }, 300);
    });

    document.getElementById('patientDropdown').addEventListener('click', function (e) {
        const opt = e.target.closest('.patient-opt');
        if (!opt) return;
        document.getElementById('selectedPatientId').value        = opt.dataset.id;
        document.getElementById('patientSelectedName').textContent = opt.dataset.name;
        document.getElementById('patientSelected').classList.remove('d-none');
        document.getElementById('patientSearch').value            = '';
        document.getElementById('patientDropdown').classList.add('d-none');
        window._patientShippingAddress = opt.dataset.shipping || '';
        if (orderType === 'delivery' && window._patientShippingAddress &&
            !document.getElementById('inputDelivery').value.trim()) {
            document.getElementById('inputDelivery').value = window._patientShippingAddress;
        }
    });

    document.getElementById('clearPatient').addEventListener('click', function () {
        document.getElementById('selectedPatientId').value = '';
        document.getElementById('patientSelected').classList.add('d-none');
        window._patientShippingAddress = '';
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('#patientSearch') && !e.target.closest('#patientDropdown'))
            document.getElementById('patientDropdown').classList.add('d-none');
    });

    /* ══════════════════════════════════════════════════════════════
       CHECKOUT — SweetAlert confirmation
    ══════════════════════════════════════════════════════════════ */
    document.getElementById('btnCheckout').addEventListener('click', function () {
        const alertEl = document.getElementById('checkoutAlert');
        alertEl.classList.add('d-none');

        if (!cart.length) {
            alertEl.textContent = 'Add at least one product to the cart.';
            alertEl.classList.remove('d-none'); return;
        }
        if (!document.getElementById('selectedPatientId').value) {
            alertEl.textContent = 'Please select a patient.';
            alertEl.classList.remove('d-none'); return;
        }
        if (orderType === 'delivery' && !document.getElementById('inputDelivery').value.trim()) {
            alertEl.textContent = 'Please enter a delivery address.';
            alertEl.classList.remove('d-none'); return;
        }

        const grand   = document.getElementById('sumGrand').textContent;
        const patient = document.getElementById('patientSelectedName').textContent;
        const itemHtml = cart.map(i =>
            `<li>${esc(i.productName)}${i.variationName ? ' <em>('+esc(i.variationName)+')</em>' : ''}`
            + ` &times; ${i.quantity} &mdash; ${currency}${i.lineTotal.toFixed(2)}</li>`
        ).join('');

        Swal.fire({
            title: editingOrderId ? 'Update Order?' : 'Place Order?',
            html: `<div style="text-align:left;font-size:.88rem;line-height:1.6">
                <p><strong>Patient:</strong> ${esc(patient)}</p>
                <p><strong>Type:</strong> ${orderType.charAt(0).toUpperCase()+orderType.slice(1)}</p>
                <p><strong>Items:</strong></p>
                <ul style="padding-left:18px;margin:0 0 8px">${itemHtml}</ul>
                <p style="font-size:.95rem;font-weight:700;color:#B1083C">Grand Total: ${currency}${grand}</p>
            </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#B1083C',
            cancelButtonColor:  '#6c757d',
            confirmButtonText: editingOrderId ? '✏️ Update' : '🛒 Confirm & Place',
            cancelButtonText: 'Cancel',
        }).then(result => { if (result.isConfirmed) placeOrder(); });
    });

    function placeOrder() {
        const btn     = document.getElementById('btnCheckout');
        const alertEl = document.getElementById('checkoutAlert');

        const payload = {
            user_id:          document.getElementById('selectedPatientId').value,
            clinic_id:        document.getElementById('selectClinic')?.value || null,
            discount:         parseFloat(document.getElementById('inputDiscount').value) || 0,
            tax_label:        document.getElementById('inputTaxLabel').value || null,
            tax_rate:         parseFloat(document.getElementById('inputTaxRate').value) || 0,
            notes:            document.getElementById('inputNotes').value || null,
            payment_status:   document.querySelector('input[name="paymentStatus"]:checked').value,
            order_type:       orderType,
            delivery_address: orderType === 'delivery'
                ? document.getElementById('inputDelivery').value.trim() : null,
            items: cart.map(i => ({
                product_id:   i.productId,
                variation_id: i.variationId ?? null,
                quantity:     i.quantity,
                unit_price:   i.unitPrice,
            })),
        };

        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing…';

        const url    = editingOrderId ? `/pos/${editingOrderId}` : '/pos';
        const method = editingOrderId ? 'PUT' : 'POST';

        fetch(url, {
            method,
            headers: {
                'Content-Type':     'application/json',
                'X-CSRF-TOKEN':     CSRF(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
            },
            body: JSON.stringify(payload),
        })
        .then(r => {
            if (r.status === 422) return r.json().then(d => { throw { validation: d.errors }; });
            if (!r.ok) throw new Error('Server error ' + r.status);
            return r.json();
        })
        .then(d => { if (d.status === 'success') window.location.href = d.redirect; })
        .catch(err => {
            btn.disabled  = false;
            btn.innerHTML = `<i class="bi bi-bag-check me-1"></i>${editingOrderId ? 'Update Order' : 'Place Order'}`;
            alertEl.innerHTML = err.validation
                ? Object.values(err.validation).flat().map(m => '&bull; ' + m).join('<br>')
                : (err.message || 'An error occurred.');
            alertEl.classList.remove('d-none');
        });
    }

    /* ══════════════════════════════════════════════════════════════
       SELECT2 — searchable dropdowns
       Using window.load to guarantee defer scripts (select2.js) have
       executed before we call .select2() on any element.
    ══════════════════════════════════════════════════════════════ */
    window.addEventListener('load', function () {
        if (typeof $.fn.select2 !== 'function') {
            console.warn('Select2 not loaded — searchable dropdowns unavailable.');
            return;
        }
        // Clinic (superadmin renders a real <select>; non-admin has a hidden input — skip)
        if ($('#selectClinic').is('select')) {
            $('#selectClinic').select2({
                placeholder: '— Select —',
                allowClear: true,
                width: '100%',
            });
        }
        // Category filter — inside a flex container, so use 'resolve' to inherit CSS width
        $('#prodCategory').select2({
            placeholder: 'All Categories',
            allowClear: true,
            width: 'resolve',
        });
    });

    /* ══════════════════════════════════════════════════════════════
       ADD PATIENT MODAL
    ══════════════════════════════════════════════════════════════ */
    document.getElementById('addPatientModal').addEventListener('show.bs.modal', function () {
        document.getElementById('newPatientName').value  = '';
        document.getElementById('newPatientPhone').value = '';
        document.getElementById('newPatientEmail').value = '';
        var al = document.getElementById('addPatientAlert');
        al.textContent = '';
        al.classList.add('d-none');
    });

    document.getElementById('btnSavePatient').addEventListener('click', function () {
        var alertEl = document.getElementById('addPatientAlert');
        alertEl.classList.add('d-none');

        var name  = document.getElementById('newPatientName').value.trim();
        var phone = document.getElementById('newPatientPhone').value.trim();
        var email = document.getElementById('newPatientEmail').value.trim();

        if (!name) {
            alertEl.textContent = 'Name is required.';
            alertEl.classList.remove('d-none');
            return;
        }

        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

        fetch('/pos/patients', {
            method: 'POST',
            headers: {
                'Content-Type':     'application/json',
                'X-CSRF-TOKEN':     CSRF(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
            },
            body: JSON.stringify({ name: name, phone: phone || null, email: email || null }),
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            // Laravel validation error returns { message, errors:{} }
            if (data.errors || (data.message && !data.id)) {
                var msg = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : data.message;
                alertEl.textContent = msg;
                alertEl.classList.remove('d-none');
                return;
            }
            // Auto-select newly created patient
            document.getElementById('selectedPatientId').value         = data.id;
            document.getElementById('patientSelectedName').textContent = data.name;
            document.getElementById('patientSelected').classList.remove('d-none');
            document.getElementById('patientSearch').value             = '';
            document.getElementById('patientDropdown').classList.add('d-none');
            // Close modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('addPatientModal'));
            if (modal) modal.hide();
        })
        .catch(function () {
            alertEl.textContent = 'Request failed. Please try again.';
            alertEl.classList.remove('d-none');
        })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-person-check me-1"></i>Save & Select';
        });
    });

    /* Utility */
    function esc(str) {
        if (!str) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();
</script>
@endsection
