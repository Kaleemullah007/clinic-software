<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Doctor <span class="text-danger">*</span></label>
        <select name="doctor_id" class="form-select border-secondary" required>
            <option value="">— Select Doctor —</option>
            @foreach($doctors as $d)
            <option value="{{ $d->id }}" {{ old('doctor_id', $agreement?->doctor_id) == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Clinic (leave blank for all)</label>
        <select name="clinic_id" class="form-select border-secondary">
            <option value="">All Clinics</option>
            @foreach($clinics as $c)
            <option value="{{ $c->id }}" {{ old('clinic_id', $agreement?->clinic_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Service (leave blank for all)</label>
        <select name="service_id" class="form-select border-secondary">
            <option value="">All Services</option>
            @foreach($services as $s)
            <option value="{{ $s->id }}" {{ old('service_id', $agreement?->service_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Share Type <span class="text-danger">*</span></label>
        <select name="share_type" class="form-select border-secondary" required>
            <option value="percentage" {{ old('share_type', $agreement?->share_type ?? 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
            <option value="fixed"      {{ old('share_type', $agreement?->share_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (PKR)</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Doctor Share <span class="text-danger">*</span></label>
        <input type="number" name="doctor_share" class="form-control border-secondary" step="0.01" min="0" required
               value="{{ old('doctor_share', $agreement?->doctor_share) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Clinic Share <span class="text-danger">*</span></label>
        <input type="number" name="clinic_share" class="form-control border-secondary" step="0.01" min="0" required
               value="{{ old('clinic_share', $agreement?->clinic_share) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Effective From <span class="text-danger">*</span></label>
        <input type="date" name="effective_from" class="form-control border-secondary" required
               value="{{ old('effective_from', $agreement?->effective_from) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Effective To (optional)</label>
        <input type="date" name="effective_to" class="form-control border-secondary"
               value="{{ old('effective_to', $agreement?->effective_to) }}">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Notes</label>
        <textarea name="notes" class="form-control border-secondary" rows="2">{{ old('notes', $agreement?->notes) }}</textarea>
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                {{ old('is_active', $agreement?->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="isActive">Active</label>
        </div>
    </div>
</div>
