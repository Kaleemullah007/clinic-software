{{-- ═══════════════════════════════════════════════════════════════════════
     Patient Records Modal  —  Prescriptions & Notes (tabbed, AJAX)
     Trigger: data-bs-toggle="modal" data-bs-target="#patientRecordsModal"
              data-appointment-id="X" data-user-id="Y" data-patient-name="Z"
════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="patientRecordsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header" style="background:linear-gradient(90deg,#B1083C,#d13729);">
                <h5 class="modal-title text-white fw-semibold">
                    <i class="bi bi-journal-medical me-2"></i>Patient Records
                    <small class="fw-normal ms-2 opacity-75" id="prm-patient-name"></small>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Tabs --}}
            <div class="modal-body p-0">
                <ul class="nav nav-tabs px-3 pt-2 border-bottom-0" id="prmTabs">
                    <li class="nav-item">
                        <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-prescriptions">
                            <i class="bi bi-capsule me-1"></i>Prescriptions
                            <span class="badge bg-danger ms-1" id="badge-prescriptions">0</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-notes">
                            <i class="bi bi-sticky me-1"></i>Notes
                            <span class="badge bg-secondary ms-1" id="badge-notes">0</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-3">

                    {{-- ── PRESCRIPTIONS TAB ── --}}
                    <div class="tab-pane fade show active" id="tab-prescriptions">
                        {{-- Add form --}}
                        <form id="prescriptionForm" class="border rounded-3 p-3 mb-3" style="background:#fafafa">
                            @csrf
                            <input type="hidden" name="type" value="prescription">
                            <input type="hidden" id="px_appointment_id" name="appointment_id">
                            <input type="hidden" id="px_user_id" name="user_id">
                            <div id="pxAlert" class="alert d-none mb-2"></div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Medicine <span class="text-danger">*</span></label>
                                    <input type="text" name="medicine" id="px_medicine" class="form-control form-control-sm border-secondary" placeholder="e.g. Panadol 500mg" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">Dosage</label>
                                    <select name="dosage" id="px_dosage" class="form-select form-select-sm border-secondary">
                                        <option value="">— Select —</option>
                                        <option>Once a day</option>
                                        <option>2 times a day</option>
                                        <option>3 times a day</option>
                                        <option>Every 2 hours</option>
                                        <option>As needed</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold small">Remarks</label>
                                    <input type="text" name="remarks" class="form-control form-control-sm border-secondary" placeholder="Additional instructions…">
                                </div>
                            </div>
                            <div class="mt-2 text-end">
                                <button type="submit" class="btn btn-sm text-white" id="pxSaveBtn"
                                        style="background:linear-gradient(90deg,#B1083C,#d13729);border:none;">
                                    <i class="bi bi-plus-circle me-1"></i>Add Prescription
                                </button>
                            </div>
                        </form>

                        {{-- Records list --}}
                        <div id="prescriptions-loading" class="text-center py-4 d-none">
                            <div class="spinner-border spinner-border-sm text-danger"></div>
                        </div>
                        <div id="prescriptions-empty" class="text-center text-muted py-4 d-none">
                            <i class="bi bi-capsule d-block fs-2 mb-2 text-muted"></i>
                            No prescriptions yet for this patient.
                        </div>
                        <div id="prescriptions-list"></div>
                    </div>

                    {{-- ── NOTES TAB ── --}}
                    <div class="tab-pane fade" id="tab-notes">
                        {{-- Add form --}}
                        <form id="noteForm" class="border rounded-3 p-3 mb-3" style="background:#fafafa">
                            @csrf
                            <input type="hidden" name="type" value="note">
                            <input type="hidden" name="medicine" value="N/A">
                            <input type="hidden" id="nt_appointment_id" name="appointment_id">
                            <input type="hidden" id="nt_user_id" name="user_id">
                            <div id="ntAlert" class="alert d-none mb-2"></div>
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Note <span class="text-danger">*</span></label>
                                    <textarea name="remarks" id="nt_remarks" class="form-control form-control-sm border-secondary" rows="2"
                                              placeholder="Write a clinical note for this patient…" required></textarea>
                                </div>
                            </div>
                            <div class="mt-2 text-end">
                                <button type="submit" class="btn btn-sm btn-secondary" id="ntSaveBtn">
                                    <i class="bi bi-plus-circle me-1"></i>Add Note
                                </button>
                            </div>
                        </form>

                        {{-- Records list --}}
                        <div id="notes-loading" class="text-center py-4 d-none">
                            <div class="spinner-border spinner-border-sm text-secondary"></div>
                        </div>
                        <div id="notes-empty" class="text-center text-muted py-4 d-none">
                            <i class="bi bi-sticky d-block fs-2 mb-2 text-muted"></i>
                            No notes yet for this patient.
                        </div>
                        <div id="notes-list"></div>
                    </div>

                </div>
            </div>

            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    let currentUserId = null;
    let currentApptId = null;

    // ── Open modal: populate hidden fields + load records ──────────────
    document.getElementById('patientRecordsModal').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        if (!btn) return;

        currentApptId  = btn.dataset.appointmentId || '';
        currentUserId  = btn.dataset.userId || '';
        const name     = btn.dataset.patientName || '';

        document.getElementById('prm-patient-name').textContent = name ? '— ' + name : '';

        // Set hidden fields on both forms
        document.getElementById('px_appointment_id').value = currentApptId;
        document.getElementById('px_user_id').value        = currentUserId;
        document.getElementById('nt_appointment_id').value = currentApptId;
        document.getElementById('nt_user_id').value        = currentUserId;

        // Reset forms
        document.getElementById('prescriptionForm').reset();
        document.getElementById('noteForm').reset();
        // Re-set hidden after reset
        document.getElementById('px_appointment_id').value = currentApptId;
        document.getElementById('px_user_id').value        = currentUserId;
        document.getElementById('nt_appointment_id').value = currentApptId;
        document.getElementById('nt_user_id').value        = currentUserId;
        document.querySelectorAll('input[name="type"]').forEach(i => i.value = i.closest('form').id === 'prescriptionForm' ? 'prescription' : 'note');

        hideAlerts();
        loadRecords(currentUserId);
    });

    // ── Load patient records via AJAX ───────────────────────────────────
    function loadRecords(userId) {
        showLoading();

        fetch('/prescriptions/patient/' + userId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            renderList('prescriptions', data.prescriptions);
            renderList('notes', data.notes);
            document.getElementById('badge-prescriptions').textContent = data.prescriptions.length;
            document.getElementById('badge-notes').textContent = data.notes.length;
        })
        .catch(() => {})
        .finally(() => hideLoading());
    }

    function showLoading() {
        ['prescriptions', 'notes'].forEach(t => {
            document.getElementById(t + '-loading').classList.remove('d-none');
            document.getElementById(t + '-list').innerHTML = '';
            document.getElementById(t + '-empty').classList.add('d-none');
        });
    }
    function hideLoading() {
        ['prescriptions', 'notes'].forEach(t => {
            document.getElementById(t + '-loading').classList.add('d-none');
        });
    }

    // ── Render list for a tab ───────────────────────────────────────────
    function renderList(type, items) {
        const list  = document.getElementById(type + '-list');
        const empty = document.getElementById(type + '-empty');

        if (!items || items.length === 0) {
            list.innerHTML = '';
            empty.classList.remove('d-none');
            return;
        }
        empty.classList.add('d-none');

        if (type === 'prescriptions') {
            list.innerHTML = `
                <div class="table-responsive">
                <table class="table table-hover table-sm align-middle" style="font-size:.85rem">
                    <thead>
                        <tr style="background:linear-gradient(90deg,#B1083C,#d13729);color:#fff">
                            <th class="ps-3">Medicine</th>
                            <th>Dosage</th>
                            <th>Remarks</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th class="text-center">Del</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map(r => `
                        <tr>
                            <td class="ps-3 fw-semibold">${esc(r.medicine)}</td>
                            <td>${esc(r.dosage || '—')}</td>
                            <td class="text-muted">${esc(r.remarks || '—')}</td>
                            <td>${esc(r.doctor)}</td>
                            <td><small>${esc(r.appt_date)}</small></td>
                            <td class="text-center">
                                ${r.can_delete ? `<button class="btn btn-sm btn-outline-danger py-0 px-1 btn-del-record" data-id="${r.id}" title="Delete"><i class="bi bi-trash3"></i></button>` : ''}
                            </td>
                        </tr>`).join('')}
                    </tbody>
                </table>
                </div>`;
        } else {
            list.innerHTML = items.map(r => `
                <div class="border rounded-3 p-3 mb-2 position-relative" style="background:#fffef5">
                    <div class="d-flex justify-content-between align-items-start">
                        <p class="mb-1" style="font-size:.88rem">${esc(r.remarks || '—')}</p>
                        ${r.can_delete ? `<button class="btn btn-sm btn-outline-danger py-0 px-1 btn-del-record ms-2 flex-shrink-0" data-id="${r.id}" title="Delete"><i class="bi bi-trash3"></i></button>` : ''}
                    </div>
                    <small class="text-muted"><i class="bi bi-person me-1"></i>${esc(r.doctor)} &nbsp;·&nbsp; ${esc(r.created_at)}</small>
                </div>`).join('');
        }
    }

    // ── Delete record ───────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-del-record');
        if (!btn) return;

        const id = btn.dataset.id;
        if (!confirm('Delete this record?')) return;

        fetch('/prescription/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams({ _method: 'DELETE' }),
        })
        .then(r => r.json())
        .then(d => { if (d.success) loadRecords(currentUserId); })
        .catch(() => {});
    });

    // ── Submit prescription form ────────────────────────────────────────
    document.getElementById('prescriptionForm').addEventListener('submit', function (e) {
        e.preventDefault();
        submitRecordForm(this, 'pxSaveBtn', 'pxAlert', 'Add Prescription');
    });

    // ── Submit note form ────────────────────────────────────────────────
    document.getElementById('noteForm').addEventListener('submit', function (e) {
        e.preventDefault();
        submitRecordForm(this, 'ntSaveBtn', 'ntAlert', 'Add Note');
    });

    function submitRecordForm(form, btnId, alertId, label) {
        const btn   = document.getElementById(btnId);
        const alert = document.getElementById(alertId);
        const orig  = btn.innerHTML;

        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';
        hideAlert(alertId);

        const data = new FormData(form);

        fetch('{{ route("prescription.store") }}', {
            method : 'POST',
            headers: {
                'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept'          : 'application/json',
            },
            body: data,
        })
        .then(r => {
            if (r.status === 422) return r.json().then(d => { throw { validation: d.errors }; });
            if (!r.ok) throw new Error('Server error');
            return r.json();
        })
        .then(d => {
            if (d.status === 'success') {
                form.reset();
                // Re-set hidden fields after reset
                document.getElementById('px_appointment_id').value = currentApptId;
                document.getElementById('px_user_id').value        = currentUserId;
                document.getElementById('nt_appointment_id').value = currentApptId;
                document.getElementById('nt_user_id').value        = currentUserId;
                loadRecords(currentUserId);
            }
        })
        .catch(err => {
            alert.className = 'alert alert-danger mb-2';
            if (err.validation) {
                alert.innerHTML = Object.values(err.validation).flat().map(m => `<div>• ${m}</div>`).join('');
            } else {
                alert.textContent = err.message || 'An error occurred.';
            }
            alert.classList.remove('d-none');
        })
        .finally(() => {
            btn.disabled  = false;
            btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>' + label;
        });
    }

    function hideAlerts() {
        ['pxAlert','ntAlert'].forEach(hideAlert);
    }
    function hideAlert(id) {
        const el = document.getElementById(id);
        if (el) { el.classList.add('d-none'); el.className = 'alert d-none mb-2'; }
    }

    function esc(str) {
        if (!str) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();
</script>
