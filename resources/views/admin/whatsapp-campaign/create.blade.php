@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="row pt-3 mx-1 align-items-center mb-3">
        <div class="col-12 d-flex align-items-center gap-3">
            <a href="{{ route('whatsapp-campaign.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-megaphone me-2" style="color:#B1083C"></i>New Campaign
            </h4>
        </div>
        <hr class="mt-2">
    </div>

    <div class="row mx-1">
        <div class="col-lg-8 col-12">
            <form action="{{ route('whatsapp-campaign.store') }}" method="POST" class="wc-form-card">
                @csrf

                {{-- Campaign Name --}}
                <div class="wc-form-section">
                    <div class="wc-section-head"><i class="bi bi-info-circle me-2"></i>Campaign Details</div>
                    <div class="wc-section-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Campaign Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control border-secondary @error('name') is-invalid @enderror"
                                placeholder="e.g. Eid Promotion 2026" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Template <span class="text-danger">*</span></label>
                            <select name="template_id" id="templateSelect" class="form-select border-secondary @error('template_id') is-invalid @enderror" required>
                                <option value="">— Select Template —</option>
                                @foreach($templates as $t)
                                    <option value="{{ $t->id }}" data-type="{{ $t->message_type }}" {{ old('template_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} ({{ ucfirst($t->message_type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('template_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div id="templatePreviewBadge" class="mt-2 d-none">
                                <span class="badge" id="templateTypeBadge" style="font-size:.8rem"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Target Audience --}}
                <div class="wc-form-section">
                    <div class="wc-section-head"><i class="bi bi-people me-2"></i>Target Audience</div>
                    <div class="wc-section-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Target Role <span class="text-danger">*</span></label>
                            <select name="target_role" class="form-select border-secondary @error('target_role') is-invalid @enderror" required>
                                <option value="">— Select Role —</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('target_role') == $role ? 'selected' : '' }}>
                                        {{ ucfirst($role) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('target_role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        @if(auth()->user()->isSuperAdmin())
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Filter by Clinic <small class="text-muted fw-normal">(optional)</small></label>
                                <select name="clinic_id" class="form-select border-secondary">
                                    <option value="">All Clinics</option>
                                    @foreach($clinics as $clinic)
                                        <option value="{{ $clinic->id }}" {{ old('clinic_id') == $clinic->id ? 'selected' : '' }}>
                                            {{ $clinic->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Filter by Doctor <small class="text-muted fw-normal">(optional)</small></label>
                                <select name="doctor_id" class="form-select border-secondary">
                                    <option value="">All Doctors</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                            {{ $doctor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Schedule --}}
                <div class="wc-form-section">
                    <div class="wc-section-head"><i class="bi bi-clock me-2"></i>Schedule & Delivery</div>
                    <div class="wc-section-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Scheduled Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="scheduled_at"
                                    class="form-control border-secondary @error('scheduled_at') is-invalid @enderror"
                                    value="{{ old('scheduled_at', now()->addMinutes(5)->format('Y-m-d\TH:i')) }}" required>
                                @error('scheduled_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Timezone <span class="text-danger">*</span></label>
                                <select name="timezone" class="form-select border-secondary @error('timezone') is-invalid @enderror" required>
                                    @foreach($timezones as $tz)
                                        <option value="{{ $tz }}" {{ old('timezone', config('services.whatsapp.timezone', 'Asia/Karachi')) === $tz ? 'selected' : '' }}>
                                            {{ $tz }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Delay Between Messages</label>
                                <div class="input-group">
                                    <input type="number" name="message_delay"
                                        class="form-control border-secondary @error('message_delay') is-invalid @enderror"
                                        min="0" max="60"
                                        value="{{ old('message_delay', config('services.whatsapp.campaign_delay', 2)) }}">
                                    <span class="input-group-text border-secondary bg-light">seconds</span>
                                </div>
                                <small class="text-muted">Delay between each message to avoid API throttling</small>
                                @error('message_delay')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wc-form-footer">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-send me-2"></i>Schedule Campaign
                    </button>
                    <a href="{{ route('whatsapp-campaign.index') }}" class="btn btn-secondary ms-2">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </a>
                </div>
            </form>
        </div>

        {{-- Tips sidebar --}}
        <div class="col-lg-4 col-12 mt-3 mt-lg-0">
            <div class="wc-tips-card">
                <div class="wc-section-head"><i class="bi bi-lightbulb me-2" style="color:#f59e0b"></i>Tips</div>
                <div class="p-3">
                    <ul class="list-unstyled mb-0" style="font-size:.85rem;line-height:2">
                        <li><i class="bi bi-check-circle text-success me-2"></i>Use <strong>2–5 second delay</strong> to avoid Meta API rate limits</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Schedule at least <strong>5 minutes ahead</strong> for queue processing</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Timezone defaults to <strong>Asia/Karachi (PKT)</strong></li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Only users with a <strong>valid phone number</strong> will receive the message</li>
                        <li><i class="bi bi-info-circle text-info me-2"></i>Make sure <code>php artisan queue:work</code> is running</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
@include('admin.whatsapp-campaign._styles')
<script>
document.getElementById('templateSelect').addEventListener('change', function () {
    const opt    = this.options[this.selectedIndex];
    const type   = opt.dataset.type;
    const badge  = document.getElementById('templateTypeBadge');
    const wrap   = document.getElementById('templatePreviewBadge');

    if (!type) { wrap.classList.add('d-none'); return; }
    wrap.classList.remove('d-none');

    const map = {
        text:  { label:'Text Only',       bg:'#0ea5e9', icon:'bi-chat-text' },
        image: { label:'Image Only',      bg:'#8b5cf6', icon:'bi-image' },
        both:  { label:'Text + Image',    bg:'#B1083C', icon:'bi-file-image' },
    };
    const m = map[type] || { label: type, bg:'#6b7280', icon:'bi-question' };
    badge.style.background = m.bg;
    badge.style.color      = '#fff';
    badge.innerHTML        = `<i class="bi ${m.icon} me-1"></i>${m.label}`;
});
</script>
@endsection
