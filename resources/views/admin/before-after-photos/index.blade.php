@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-images me-2 text-theme-color"></i>Before / After Photos</h4>
        </div>
        <hr class="my-2">
    </div>

    @include('flash-message')

    {{-- Upload Form --}}
    @can('before-after-photos.create')
    <div class="row mx-1 mb-4">
        <div class="col-lg-5 col-12">
            <div class="shadow-css p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-cloud-upload me-1 text-theme-color"></i> Upload Photo</h6>
                <form method="POST" action="{{ route('before-after-photos.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Appointment <span class="text-danger">*</span></label>
                            <select name="appointment_id" id="apptSelect" class="form-select border-secondary" required>
                                <option value="">— Select Appointment —</option>
                                @foreach($appointments as $a)
                                <option value="{{ $a->id }}" data-patient="{{ $a->user_id }}">
                                    #{{ $a->id }} — {{ $a->patient->name ?? $a->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Patient ID <span class="text-danger">*</span></label>
                            <input type="number" name="patient_id" id="patientIdField"
                                   class="form-control border-secondary" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Photo Type <span class="text-danger">*</span></label>
                            <select name="photo_type" class="form-select border-secondary" required>
                                <option value="before">Before</option>
                                <option value="after">After</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Photo <span class="text-danger">*</span></label>
                            <input type="file" name="photo" id="photoInput"
                                   class="form-control border-secondary" accept="image/*" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Caption</label>
                            <input type="text" name="caption" class="form-control border-secondary"
                                   placeholder="Optional description">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="patient_consent" value="1"
                                       class="form-check-input" id="consentCheck" checked>
                                <label class="form-check-label" for="consentCheck">
                                    Patient has given consent to store this photo
                                </label>
                            </div>
                        </div>
                        <div class="col-12" id="previewBox" style="display:none">
                            <img id="previewImg" src="" class="img-fluid rounded border"
                                 style="max-height:160px">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-theme btn-sm">
                            <i class="bi bi-upload me-1"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    {{-- Filter bar --}}
    <div class="row mx-1 mb-3">
        <div class="col-12">
            <form method="GET" class="d-flex gap-2 flex-wrap">
                <select name="type" class="form-select border-secondary form-select-sm" style="width:auto">
                    <option value="">All Types</option>
                    <option value="before" {{ request('type')=='before'?'selected':'' }}>Before</option>
                    <option value="after"  {{ request('type')=='after' ?'selected':'' }}>After</option>
                </select>
                <input type="number" name="appointment_id"
                       class="form-control border-secondary form-control-sm" style="width:160px"
                       placeholder="Appointment ID" value="{{ request('appointment_id') }}">
                <button class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                @if(request()->hasAny(['type','appointment_id']))
                <a href="{{ route('before-after-photos.index') }}" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Gallery --}}
    <div class="row mx-1">
        <div class="col-12">
            @if($photos->isEmpty())
            <div class="shadow-css p-4 text-center text-muted">
                <i class="bi bi-image fs-1 d-block mb-2"></i>No photos found.
            </div>
            @else
            <div class="row g-3">
                @foreach($photos as $photo)
                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                    <div class="photo-card shadow-css overflow-hidden">
                        <div class="position-relative">
                            <img src="{{ Storage::url($photo->file_path) }}"
                                 class="w-100 photo-thumb"
                                 style="height:150px;object-fit:cover;cursor:pointer"
                                 onclick="showLightbox('{{ Storage::url($photo->file_path) }}','{{ addslashes($photo->caption ?? '') }}')"
                                 alt="{{ $photo->caption }}">
                            <span class="badge position-absolute top-0 start-0 m-1
                                {{ $photo->photo_type=='before' ? 'bg-warning text-dark' : 'bg-success' }}">
                                {{ ucfirst($photo->photo_type) }}
                            </span>
                            @if(!$photo->patient_consent)
                            <span class="badge bg-danger position-absolute top-0 end-0 m-1"
                                  title="No consent recorded">
                                <i class="bi bi-exclamation-triangle"></i>
                            </span>
                            @endif
                        </div>
                        <div class="p-2">
                            <p class="mb-0 small fw-semibold text-truncate"
                               title="{{ $photo->appointment->patient->name ?? '—' }}">
                                {{ $photo->appointment->patient->name ?? '—' }}
                            </p>
                            <p class="mb-0 text-muted" style="font-size:.7rem">
                                Appt #{{ $photo->appointment_id }} &bull;
                                {{ $photo->created_at->format('d M Y') }}
                            </p>
                            @if($photo->caption)
                            <p class="mb-0 text-muted text-truncate"
                               style="font-size:.7rem"
                               title="{{ $photo->caption }}">{{ $photo->caption }}</p>
                            @endif
                            @can('before-after-photos.delete')
                            <form action="{{ route('before-after-photos.destroy',$photo) }}"
                                  method="POST" class="mt-1"
                                  onsubmit="return confirm('Delete this photo?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger w-100"
                                        style="font-size:.7rem;padding:2px 4px">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-3">{{ $photos->links() }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Lightbox --}}
<div id="lightbox" onclick="this.style.display='none'"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:9999;
            align-items:center;justify-content:center;">
    <div onclick="event.stopPropagation()" style="position:relative;max-width:90vw;text-align:center">
        <button class="btn-close btn-close-white position-absolute top-0 end-0 m-2"
                onclick="document.getElementById('lightbox').style.display='none'"></button>
        <img id="lightboxImg" src="" class="img-fluid rounded" style="max-height:82vh">
        <p id="lightboxCaption" class="text-white text-center mt-2 mb-0 small"></p>
    </div>
</div>
@endsection

@section('script')
<style>
    .text-theme-color { color: #B1083C; }
    .btn-theme { background: linear-gradient(90deg, #B1083C, #d13729); color: #fff; border: none; }
    .shadow-css  { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    .photo-card  { border-radius: 8px; transition: .2s; }
    .photo-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.18) !important; }
    .photo-thumb { transition: opacity .15s; }
    .photo-thumb:hover { opacity: .88; }
</style>

<script>
// Auto-fill patient ID when appointment is selected
document.getElementById('apptSelect')?.addEventListener('change', function () {
    const pid = this.options[this.selectedIndex]?.dataset?.patient;
    if (pid) document.getElementById('patientIdField').value = pid;
});

// Preview image before upload
document.getElementById('photoInput')?.addEventListener('change', function () {
    const box = document.getElementById('previewBox');
    const img = document.getElementById('previewImg');
    if (this.files && this.files[0]) {
        img.src = URL.createObjectURL(this.files[0]);
        box.style.display = 'block';
    }
});

function showLightbox(src, caption) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxCaption').textContent = caption || '';
    const lb = document.getElementById('lightbox');
    lb.style.display = 'flex';
}
</script>
@endsection
