@extends('layouts.admin')

@section('content')
<div class="container-fluid pb-5">

    {{-- ── Page Header ──────────────────────────────────────────────────── --}}
    <div class="row pt-3 mx-1 align-items-center">
        <div class="col">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-bag-plus me-2" style="color:#B1083C;"></i>
                Add Products to Appointment
            </h4>
        </div>
        <div class="col-auto">
            @if($appointment)
                <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Appointment
                </a>
            @else
                <a href="{{ route('appointment-products.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            @endif
        </div>
    </div>
    <hr class="mx-1 mt-2 mb-3">

    <div class="row mx-1 g-4">

        {{-- ── Left: Form ───────────────────────────────────────────────── --}}
        <div class="col-lg-7 col-12">
            <div class="card border-0 shadow-sm">

                {{-- Patient info banner --}}
                @if($appointment)
                <div class="card-header text-white py-3" style="background:linear-gradient(90deg,#B1083C,#d13729);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                             style="width:44px;height:44px;flex-shrink:0;">
                            <i class="bi bi-person-fill fs-5 text-white"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-6">{{ $appointment->name ?? ($appointment->patient->name ?? 'Patient') }}</div>
                            <div class="small opacity-75">
                                Appointment #{{ $appointment->id }}
                                @if($appointment->date) &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }} @endif
                                @if($appointment->phone) &nbsp;·&nbsp; {{ $appointment->phone }} @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card-body p-4">
                    @include('flash-message')

                    {{-- Hidden encrypted appointment token --}}
                    @if(!$appointment)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        No appointment selected. Please go back and open this page from an appointment row.
                    </div>
                    @endif

                    <form method="POST" action="{{ route('appointment-products.store') }}" id="apptProductsForm">
                        @csrf
                        <input type="hidden" name="appointment_token" value="{{ $appointmentToken }}">

                        {{-- Global deduct inventory toggle --}}
                        <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-light rounded">
                            <label class="form-check-label fw-semibold mb-0" for="deductCheck">
                                <i class="bi bi-box-arrow-in-down me-1 text-danger"></i>Deduct from Inventory
                            </label>
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" class="form-check-input" role="switch"
                                       id="deductCheck" name="deduct_inventory" value="1" checked>
                            </div>
                        </div>

                        {{-- ── Product rows ────────────────────────────────── --}}
                        <div id="productRows"></div>

                        {{-- ── Grand total ─────────────────────────────────── --}}
                        <div class="d-flex justify-content-between align-items-center p-3 rounded mt-3"
                             style="background:linear-gradient(90deg,#B1083C15,#d1372910);">
                            <span class="fw-bold fs-6">Grand Total</span>
                            <span class="fw-bold fs-5" style="color:#B1083C;" id="grandTotal">PKR 0.00</span>
                        </div>

                        {{-- ── Add More + Submit ────────────────────────────── --}}
                        <div class="d-flex gap-2 mt-4 flex-wrap">
                            <button type="button" id="addMoreBtn" class="btn btn-outline-secondary">
                                <i class="bi bi-plus-circle me-1"></i>Add Another Product
                            </button>
                            <button type="submit" class="btn text-white px-4" id="saveAllBtn"
                                    style="background:linear-gradient(90deg,#B1083C,#d13729);border:none;"
                                    @unless($appointment) disabled @endunless>
                                <i class="bi bi-save me-1"></i>Save All Products
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- ── Right: Existing products on this appointment ─────────────── --}}
        <div class="col-lg-5 col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header fw-semibold py-3" style="background:#f8f9fa;">
                    <i class="bi bi-bag-check me-2" style="color:#B1083C;"></i>
                    Products on This Appointment
                    <span class="badge ms-2" style="background:#B1083C;" id="existingBadge">
                        {{ $existingItems->count() }}
                    </span>
                </div>
                <div class="card-body p-0">
                    <div id="existingProductsPanel">
                        @if($existingItems->count())
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $existingTotal = 0; @endphp
                                @foreach($existingItems as $ei)
                                @php $existingTotal += $ei->total_price; @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold small">{{ $ei->product_name }}</div>
                                        @if($ei->variation)
                                            <small class="text-muted">{{ $ei->variation->name }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $ei->quantity }}</td>
                                    <td class="text-end small fw-semibold" style="color:#B1083C;">
                                        PKR {{ number_format($ei->total_price, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="fw-bold text-end">Total:</td>
                                    <td class="fw-bold text-end" style="color:#B1083C;">
                                        PKR {{ number_format($existingTotal, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            No products added yet
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── Hidden template for one product row ──────────────────────────── --}}
<template id="rowTemplate">
    <div class="product-row card border border-secondary-subtle rounded-3 mb-3" data-index="__IDX__">
        <div class="card-body pb-2 pt-3 px-3">

            {{-- Row header --}}
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-semibold small text-muted">
                    <i class="bi bi-box me-1"></i>Product <span class="row-num">1</span>
                </span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-row py-0 px-2"
                        style="display:none;" title="Remove">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- Duplicate warning --}}
            <div class="alert alert-warning alert-sm py-1 px-2 small mb-2 dup-warning d-none">
                <i class="bi bi-exclamation-triangle me-1"></i>
                This product is already on the appointment — quantity will be <strong>added</strong> to the existing entry.
            </div>

            <div class="row g-2">

                {{-- Product select --}}
                <div class="col-md-7 col-12">
                    <label class="form-label small fw-semibold mb-1">Product</label>
                    <select name="products[__IDX__][product_id]" class="form-select form-select-sm border-secondary product-select">
                        <option value="">— Free text below —</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}"
                                data-price="{{ $p->price }}"
                                data-name="{{ $p->name }}"
                                data-has-variations="{{ $p->has_variations ? 1 : 0 }}"
                                data-variations="{{ $p->variations->map(fn($v)=>['id'=>$v->id,'name'=>$v->name,'price'=>$v->price])->toJson() }}">
                            {{ $p->name }} (PKR {{ number_format($p->price,2) }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Variation --}}
                <div class="col-md-5 col-12 variation-wrap d-none">
                    <label class="form-label small fw-semibold mb-1">Variation</label>
                    <select name="products[__IDX__][variation_id]" class="form-select form-select-sm border-secondary variation-select">
                        <option value="">— Select —</option>
                    </select>
                </div>

                {{-- Product name free text --}}
                <div class="col-12">
                    <label class="form-label small fw-semibold mb-1">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="products[__IDX__][product_name]"
                           class="form-control form-control-sm border-secondary product-name"
                           placeholder="Auto-filled from dropdown, or type manually" required>
                </div>

                {{-- Code --}}
                <div class="col-md-4 col-6">
                    <label class="form-label small fw-semibold mb-1">Code</label>
                    <input type="text" name="products[__IDX__][product_code]"
                           class="form-control form-control-sm border-secondary product-code">
                </div>

                {{-- Qty --}}
                <div class="col-md-4 col-6">
                    <label class="form-label small fw-semibold mb-1">Qty <span class="text-danger">*</span></label>
                    <input type="number" name="products[__IDX__][quantity]"
                           class="form-control form-control-sm border-secondary row-qty"
                           step="0.01" min="0.01" value="1" required>
                </div>

                {{-- Unit price --}}
                <div class="col-md-4 col-12">
                    <label class="form-label small fw-semibold mb-1">Unit Price <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">PKR</span>
                        <input type="number" name="products[__IDX__][unit_price]"
                               class="form-control border-secondary row-price"
                               step="0.01" min="0" value="0" required>
                    </div>
                </div>

                {{-- Row subtotal --}}
                <div class="col-12">
                    <div class="d-flex justify-content-between bg-light rounded px-3 py-2">
                        <span class="small text-muted">Row Total</span>
                        <span class="small fw-bold row-total" style="color:#B1083C;">PKR 0.00</span>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="col-12">
                    <label class="form-label small fw-semibold mb-1">Notes</label>
                    <textarea name="products[__IDX__][notes]"
                              class="form-control form-control-sm border-secondary" rows="1"
                              placeholder="Optional…"></textarea>
                </div>

            </div>
        </div>
    </div>
</template>

@endsection

@section('script')
<style>
    .product-row { transition: box-shadow .15s; }
    .product-row:hover { box-shadow: 0 2px 10px rgba(177,8,60,.12) !important; }
</style>

<script>
(function () {
    // All products keyed by id (for JS logic)
    const PRODUCTS      = @json($products->keyBy('id'));
    // Existing product_ids already on this appointment
    const EXISTING_IDS  = @json($existingItems->pluck('product_id')->filter()->values());

    const rowsContainer = document.getElementById('productRows');
    const grandTotalEl  = document.getElementById('grandTotal');
    let   rowCount      = 0;

    // ── Format number ───────────────────────────────────────────────────
    function fmt(n) {
        return 'PKR ' + parseFloat(n || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    // ── Recalculate grand total ──────────────────────────────────────────
    function updateGrand() {
        let total = 0;
        document.querySelectorAll('.row-total').forEach(el => {
            total += parseFloat(el.dataset.value || 0);
        });
        grandTotalEl.textContent = fmt(total);
    }

    // ── Wire up a single product row ─────────────────────────────────────
    function wireRow(row) {
        const productSel  = row.querySelector('.product-select');
        const variWrap    = row.querySelector('.variation-wrap');
        const variSel     = row.querySelector('.variation-select');
        const nameField   = row.querySelector('.product-name');
        const qtyField    = row.querySelector('.row-qty');
        const priceField  = row.querySelector('.row-price');
        const rowTotalEl  = row.querySelector('.row-total');
        const removeBtn   = row.querySelector('.remove-row');
        const dupWarning  = row.querySelector('.dup-warning');

        function calcRowTotal() {
            const val = (parseFloat(qtyField.value) || 0) * (parseFloat(priceField.value) || 0);
            rowTotalEl.textContent  = fmt(val);
            rowTotalEl.dataset.value = val;
            updateGrand();
        }

        // Product select change
        productSel.addEventListener('change', function () {
            const p = PRODUCTS[this.value];
            if (!p) {
                variWrap.classList.add('d-none');
                dupWarning.classList.add('d-none');
                return;
            }
            // Fill name & price
            nameField.value  = p.name;
            priceField.value = p.price;
            calcRowTotal();

            // Duplicate warning
            if (EXISTING_IDS.includes(parseInt(this.value))) {
                dupWarning.classList.remove('d-none');
            } else {
                dupWarning.classList.add('d-none');
            }

            // Variations
            variSel.innerHTML = '<option value="">— Select variation —</option>';
            if (p.has_variations && p.variations && p.variations.length) {
                p.variations.forEach(v => {
                    variSel.insertAdjacentHTML('beforeend',
                        `<option value="${v.id}" data-price="${v.price}">${v.name} — PKR ${parseFloat(v.price).toFixed(2)}</option>`);
                });
                variWrap.classList.remove('d-none');
            } else {
                variWrap.classList.add('d-none');
            }
        });

        variSel.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.price) {
                priceField.value = opt.dataset.price;
                calcRowTotal();
            }
        });

        qtyField.addEventListener('input', calcRowTotal);
        priceField.addEventListener('input', calcRowTotal);

        removeBtn.addEventListener('click', function () {
            row.remove();
            updateGrand();
            // Re-number rows
            document.querySelectorAll('.product-row').forEach((r, i) => {
                r.querySelector('.row-num').textContent = i + 1;
                // Show/hide remove button
                r.querySelector('.remove-row').style.display = i === 0 ? 'none' : '';
            });
        });
    }

    // ── Add a new row ────────────────────────────────────────────────────
    function addRow() {
        const tmpl  = document.getElementById('rowTemplate');
        const clone = tmpl.content.cloneNode(true);
        const div   = clone.querySelector('.product-row');

        // Replace __IDX__ placeholder in name attributes
        div.innerHTML = div.innerHTML.replace(/__IDX__/g, rowCount);
        div.dataset.index = rowCount;
        div.querySelector('.row-num').textContent = rowCount + 1;

        // Show remove button for rows after the first
        if (rowCount > 0) {
            div.querySelector('.remove-row').style.display = '';
        }

        rowsContainer.appendChild(div);
        wireRow(div);
        rowCount++;
    }

    // Add first row on load
    addRow();

    // Add more button
    document.getElementById('addMoreBtn').addEventListener('click', addRow);

    // ── AJAX submit ──────────────────────────────────────────────────────
    document.getElementById('apptProductsForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const saveBtn = document.getElementById('saveAllBtn');
        saveBtn.disabled  = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

        const formData = new FormData(this);

        fetch(this.action, {
            method : 'POST',
            headers: {
                'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept'          : 'application/json',
            },
            body: formData,
        })
        .then(r => {
            if (r.status === 422) return r.json().then(d => { throw { validation: d.errors }; });
            if (!r.ok) throw new Error('Server error (' + r.status + ')');
            return r.json();
        })
        .then(data => {
            if (data.success) {
                // Redirect to appointment show
                @if($appointment)
                window.location.href = '{{ route("appointments.show", $appointment->id) }}?saved=1';
                @else
                window.location.href = '{{ route("appointment-products.index") }}';
                @endif
            } else {
                throw new Error(data.message || 'Unknown error.');
            }
        })
        .catch(err => {
            saveBtn.disabled  = false;
            saveBtn.innerHTML = '<i class="bi bi-save me-1"></i>Save All Products';

            let msg = 'An error occurred. Please try again.';
            if (err.validation) {
                msg = Object.values(err.validation).flat().join('\n');
            } else if (err.message) {
                msg = err.message;
            }
            alert(msg);
        });
    });

})();
</script>
@endsection
