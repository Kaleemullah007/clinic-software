<div class="modal fade" id="createPrescription" tabindex="-1" aria-labelledby="createPrescriptionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(90deg,#B1083C,#d13729);">
                <h5 class="modal-title text-white fw-semibold" id="createPrescriptionLabel">
                    <i class="bi bi-capsule me-2"></i>Create Prescription
                    <small class="fw-normal ms-2 opacity-75" id="prescriptionPatientName"></small>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="prescriptionForm">
                @csrf
                {{-- Hidden fields set by the trigger button --}}
                <input type="hidden" id="px_appointment_id" name="appointment_id" value="">
                <input type="hidden" id="px_user_id"        name="user_id"        value="">

                <div class="modal-body">

                    {{-- Alert area --}}
                    <div id="prescriptionAlert" class="alert d-none mb-3"></div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="px_medicine" class="form-label fw-semibold">Medicine <span class="text-danger">*</span></label>
                            <select class="form-select border-secondary" id="px_medicine" name="medicine" required>
                                <option value="">— Select Medicine —</option>
                                <option value="Aspirin">Aspirin</option>
                                <option value="Nims">Nims</option>
                                <option value="Panadol">Panadol</option>
                                <option value="Ibuprofen">Ibuprofen</option>
                                <option value="Amoxicillin">Amoxicillin</option>
                                <option value="Other">Other (see remarks)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="px_dosage" class="form-label fw-semibold">Dosage</label>
                            <select class="form-select border-secondary" id="px_dosage" name="dosage">
                                <option value="">— Select Dosage —</option>
                                <option value="Once a day">Once a day</option>
                                <option value="2 times a day">2 times a day</option>
                                <option value="3 times a day">3 times a day</option>
                                <option value="Every 2 hours">Every 2 hours</option>
                                <option value="Every 3 hours">Every 3 hours</option>
                                <option value="As needed">As needed</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="px_remarks" class="form-label fw-semibold">Remarks / Notes</label>
                            <textarea class="form-control border-secondary" id="px_remarks" name="remarks"
                                      rows="3" placeholder="Additional instructions, allergies, notes…"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn text-white" id="prescriptionSaveBtn"
                            style="background:linear-gradient(90deg,#B1083C,#d13729);border:none;">
                        <i class="bi bi-save me-1"></i>Save Prescription
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
// Populate hidden fields when the modal is triggered from a row button
document.addEventListener('show.bs.modal', function (e) {
    if (e.target.id !== 'createPrescription') return;
    const btn = e.relatedTarget;
    if (!btn) return;

    document.getElementById('px_appointment_id').value = btn.dataset.appointmentId || '';
    document.getElementById('px_user_id').value        = btn.dataset.userId        || '';
    document.getElementById('prescriptionPatientName').textContent =
        btn.dataset.patientName ? '— ' + btn.dataset.patientName : '';

    // Reset form fields & alert on re-open
    document.getElementById('prescriptionForm').reset();
    // Re-set hidden after reset
    document.getElementById('px_appointment_id').value = btn.dataset.appointmentId || '';
    document.getElementById('px_user_id').value        = btn.dataset.userId        || '';
    const alert = document.getElementById('prescriptionAlert');
    alert.classList.add('d-none');
    alert.className = 'alert d-none mb-3';
});

// AJAX submit
document.getElementById('prescriptionForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const btn   = document.getElementById('prescriptionSaveBtn');
    const alert = document.getElementById('prescriptionAlert');

    btn.disabled    = true;
    btn.innerHTML   = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';
    alert.classList.add('d-none');

    const formData = new FormData(this);

    fetch('{{ route("prescription.store") }}', {
        method : 'POST',
        headers: {
            'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(r => {
        if (r.status === 422) {
            return r.json().then(data => { throw { validation: data.errors }; });
        }
        if (!r.ok) throw new Error('Server error (' + r.status + ')');
        return r.json();
    })
    .then(data => {
        if (data.status === 'success') {
            alert.className   = 'alert alert-success mb-3';
            alert.textContent = data.message || 'Prescription saved successfully!';
            alert.classList.remove('d-none');
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('createPrescription')).hide();
            }, 1200);
        } else {
            throw new Error(data.message || 'Failed to save.');
        }
    })
    .catch(err => {
        alert.className = 'alert alert-danger mb-3';
        if (err.validation) {
            // Flatten Laravel validation error messages
            const msgs = Object.values(err.validation).flat();
            alert.innerHTML = msgs.map(m => `<div>• ${m}</div>`).join('');
        } else {
            alert.textContent = err.message || 'An error occurred. Please try again.';
        }
        alert.classList.remove('d-none');
    })
    .finally(() => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="bi bi-save me-1"></i>Save Prescription';
    });
});
</script>
