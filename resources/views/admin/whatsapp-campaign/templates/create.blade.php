@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="row pt-3 mx-1 align-items-center mb-3">
        <div class="col-12 d-flex align-items-center gap-3">
            <a href="{{ route('whatsapp-campaign.templates') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-file-earmark-text me-2" style="color:#B1083C"></i>
                {{ isset($template) ? 'Edit Template' : 'New Template' }}
            </h4>
        </div>
        <hr class="mt-2">
    </div>

    <div class="row mx-1">
        <div class="col-lg-7 col-12">
            <form action="{{ isset($template) ? route('whatsapp-campaign.templates.update', $template->id) : route('whatsapp-campaign.templates.store') }}"
                  method="POST" enctype="multipart/form-data" class="wc-form-card" id="templateForm">
                @csrf
                @if(isset($template)) @method('PUT') @endif

                <div class="wc-form-section">
                    <div class="wc-section-head"><i class="bi bi-info-circle me-2"></i>Template Details</div>
                    <div class="wc-section-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control border-secondary @error('name') is-invalid @enderror"
                                placeholder="e.g. Eid Mubarak Greeting"
                                value="{{ old('name', $template->name ?? '') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Message Type <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 flex-wrap">
                                @foreach(['text' => 'Text Only', 'image' => 'Image Only', 'both' => 'Text + Image'] as $val => $label)
                                <label class="wc-type-card {{ old('message_type', $template->message_type ?? '') === $val ? 'selected' : '' }}" id="type-{{ $val }}">
                                    <input type="radio" name="message_type" value="{{ $val }}"
                                        class="d-none" {{ old('message_type', $template->message_type ?? '') === $val ? 'checked' : '' }}>
                                    <i class="bi {{ $val === 'text' ? 'bi-chat-text' : ($val === 'image' ? 'bi-image' : 'bi-file-image') }} me-1"></i>
                                    {{ $label }}
                                </label>
                                @endforeach
                            </div>
                            @error('message_type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Message body --}}
                        <div id="bodySection" class="mb-3 {{ old('message_type', $template->message_type ?? 'text') === 'image' ? 'd-none' : '' }}">
                            <label class="form-label fw-semibold">Message Text <span class="text-danger" id="bodyRequired">*</span></label>
                            <textarea name="message_body" rows="5"
                                class="form-control border-secondary @error('message_body') is-invalid @enderror"
                                placeholder="Write your promotional message here…">{{ old('message_body', $template->message_body ?? '') }}</textarea>
                            <small class="text-muted">You can use patient name with <code>{name}</code> variable (coming soon)</small>
                            @error('message_body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Image upload --}}
                        <div id="imageSection" class="mb-3 {{ old('message_type', $template->message_type ?? 'text') === 'text' ? 'd-none' : '' }}">
                            <label class="form-label fw-semibold">Campaign Image <span class="text-danger" id="imgRequired">*</span></label>
                            <input type="file" name="image" id="imageInput" accept="image/*"
                                class="form-control border-secondary @error('image') is-invalid @enderror">
                            <small class="text-muted">Max 5MB. JPG, PNG, WebP supported.</small>
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                            {{-- Existing image --}}
                            @if(isset($template) && $template->image_path)
                            <div class="mt-2" id="existingImg">
                                <small class="text-muted d-block mb-1">Current image:</small>
                                <img src="{{ $template->image_url }}" alt="Current" class="rounded border"
                                    style="max-height:120px;max-width:200px;object-fit:cover">
                            </div>
                            @endif

                            {{-- Preview of newly selected image --}}
                            <div id="imgPreviewWrap" class="mt-2 d-none">
                                <small class="text-muted d-block mb-1">New image preview:</small>
                                <img id="imgPreviewLocal" src="" alt="" class="rounded border"
                                    style="max-height:120px;max-width:200px;object-fit:cover">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select border-secondary" required>
                                <option value="active"   {{ old('status', $template->status ?? 'active') === 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $template->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="wc-form-footer">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-save me-1"></i>{{ isset($template) ? 'Update Template' : 'Save Template' }}
                    </button>
                    <a href="{{ route('whatsapp-campaign.templates') }}" class="btn btn-secondary ms-2">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </a>
                </div>
            </form>
        </div>

        {{-- WhatsApp-style preview --}}
        <div class="col-lg-5 col-12 mt-3 mt-lg-0">
            <div class="wc-form-card">
                <div class="wc-section-head"><i class="bi bi-whatsapp me-2" style="color:#25d366"></i>Live Preview</div>
                <div class="p-3">
                    <div class="wc-whatsapp-preview" id="livePreview">
                        <div class="text-center text-muted py-4 small" id="previewEmpty">
                            <i class="bi bi-chat-dots d-block fs-3 mb-2"></i>
                            Fill in the form to see a preview
                        </div>
                        <div id="previewContent" class="d-none">
                            <img id="pvImg" src="" class="img-fluid rounded mb-2 d-none" style="max-height:180px;width:100%;object-fit:cover">
                            <p id="pvText" class="mb-0 small" style="white-space:pre-line"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
@include('admin.whatsapp-campaign._styles')
<script>
// ── Message type toggle ──────────────────────────────────────────────
document.querySelectorAll('.wc-type-card').forEach(function (card) {
    card.addEventListener('click', function () {
        document.querySelectorAll('.wc-type-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector('input').checked = true;
        updateSections();
        updatePreview();
    });
});

function getType() {
    const checked = document.querySelector('input[name="message_type"]:checked');
    return checked ? checked.value : 'text';
}

function updateSections() {
    const t = getType();
    document.getElementById('bodySection').classList.toggle('d-none', t === 'image');
    document.getElementById('imageSection').classList.toggle('d-none', t === 'text');
}

// ── Image preview ────────────────────────────────────────────────────
document.getElementById('imageInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const url = URL.createObjectURL(file);
    document.getElementById('imgPreviewLocal').src = url;
    document.getElementById('imgPreviewWrap').classList.remove('d-none');
    document.getElementById('pvImg').src = url;
    updatePreview();
});

// ── Live text preview ────────────────────────────────────────────────
document.querySelector('textarea[name="message_body"]').addEventListener('input', updatePreview);

function updatePreview() {
    const t    = getType();
    const text = document.querySelector('textarea[name="message_body"]').value.trim();
    const img  = document.getElementById('pvImg').src;
    const empty  = document.getElementById('previewEmpty');
    const content = document.getElementById('previewContent');
    const pvImg  = document.getElementById('pvImg');
    const pvText = document.getElementById('pvText');

    const hasText  = text.length > 0;
    const hasImg   = pvImg.src && !pvImg.src.endsWith('#');

    if (!hasText && !hasImg) {
        empty.classList.remove('d-none');
        content.classList.add('d-none');
        return;
    }
    empty.classList.add('d-none');
    content.classList.remove('d-none');

    pvImg.classList.toggle('d-none', t === 'text' || !hasImg);
    pvText.classList.toggle('d-none', t === 'image');
    pvText.textContent = text;
}

// Init on page load (for edit mode)
updateSections();
updatePreview();
</script>
@endsection
