<div class="modal fade" id="editPrescription" tabindex="-1" aria-labelledby="editPrescriptionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editPrescriptionLabel">Edit Prescription</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @include('flash-message')

            <form method="POST"  id="updateForm" enctype="">

                <div class="modal-body">

                    @csrf
                    @method('PUT')
                    <input type="hidden" value="0" name="id" id="id">
                    <div class="pb-3">
                        <label for="remarks" class="form-label fs-6">Remarks</label>
                        <input type="text"
                            class="form-control bg-grey border-secondary @error('remarks') is-invalid @enderror"
                            id="remarks" name="remarks" value="{{ old('remarks') }}"
                            autocomplete="remarks" required>
                        @error('remarks')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="pb-3">
                        <label for="medicine" class="form-label fs-6">Medicine</label>
                        <select
                            class="form-select bg-grey border-secondary @error('medicine') is-invalid @enderror"
                            id="medicine" name="medicine" autocomplete="medicine">
                            <option value="1" @if (old('medicine') == 1) 'selected' @endif>Select
                            </option>
                            <option value="2" @if (old('medicine') == 2) 'selected' @endif>Asprine
                            </option>
                            <option value="3" @if (old('medicine') == 3) 'selected' @endif>Nims
                            </option>
                            <option value="4" @if (old('medicine') == 3) 'selected' @endif>Panadol
                            </option>
                        </select>
                        @error('medicine')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="pb-3">
                        <label for="dosage" class="form-label fs-6">Dosage</label>
                        <select
                            class="form-select bg-grey border-secondary @error('dosage') is-invalid @enderror"
                            id="dosage" name="dosage" autocomplete="dosage">
                            <option value="1" @if (old('dosage') == 1) 'selected' @endif>Select
                            </option>
                            <option value="2" @if (old('dosage') == 2) 'selected' @endif>Once a day
                            </option>
                            <option value="3" @if (old('dosage') == 3) 'selected' @endif>2 times a day
                            </option>
                            <option value="4" @if (old('dosage') == 4) 'selected' @endif>3 times a day
                            </option>
                            <option value="5" @if (old('dosage') == 5) 'selected' @endif>Every 2 hours
                            </option>
                            <option value="6" @if (old('dosage') == 6) 'selected' @endif>Every 3 hours
                            </option>
                        </select>
                        @error('dosage')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-md ms-2" data-bs-dismiss="modal"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                    <button type="submit" class="btn btn-success btn-md text-white"><i class="bi bi-save me-2"></i>Save</button>
                </div>
            </form>

        </div>
    </div>
</div>
