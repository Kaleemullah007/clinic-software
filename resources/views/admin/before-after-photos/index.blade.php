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
        <div class="col-lg-6 col-12">
            <div class="shadow-css p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-cloud-upload me-1 text-theme-color"></i> Upload Photos</h6>
                <form method="POST" action="{{ route('before-after-photos.store') }}"
                      enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="row g-3">

                        {{-- Appointment --}}
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

                        {{-- Patient ID (hidden, auto-filled) --}}
                        <input type="hidden" name="patient_id" id="patientIdField">

                        {{-- Photo Type --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Photo Type <span class="text-danger">*</span></label>
                            <select name="photo_type" class="form-select border-secondary" required>
                                <option value="before">Before</option>
                                <option value="after">After</option>
                            </select>
                        </div>

                        {{-- Caption --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Caption</label>
                            <input type="text" name="caption" class="form-control border-secondary"
                                   placeholder="Optional description">
                        </div>

                        {{-- Photo Source Buttons --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Photos <span class="text-danger">*</span>
                                <small class="text-muted fw-normal ms-1">(select multiple or use camera)</small>
                            </label>
                            <div class="d-flex gap-2 flex-wrap">
                                {{-- Hidden multi-file input --}}
                                <input type="file" name="photos[]" id="photoInput"
                                       accept="image/*" multiple class="d-none">

                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="document.getElementById('photoInput').click()">
                                    <i class="bi bi-images me-1"></i> Choose Files
                                </button>

                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        id="openCameraBtn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#cameraModal">
                                    <i class="bi bi-camera-video me-1"></i> Use Camera
                                </button>
                            </div>
                        </div>

                        {{-- Queue preview grid --}}
                        <div class="col-12" id="previewGrid" style="display:none">
                            <div class="d-flex gap-2 flex-wrap" id="previewList"></div>
                        </div>

                        {{-- Consent --}}
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="patient_consent" value="1"
                                       class="form-check-input" id="consentCheck" checked>
                                <label class="form-check-label" for="consentCheck">
                                    Patient has given consent to store this photo
                                </label>
                            </div>
                        </div>

                    </div>
                    <div class="mt-3 d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-theme btn-sm" id="uploadBtn" disabled>
                            <i class="bi bi-upload me-1"></i> Upload
                            <span id="uploadCount" class="badge bg-white ms-1" style="color:#B1083C;display:none"></span>
                        </button>
                        <span class="text-muted small" id="queueStatus"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    {{-- Camera Modal --}}
    <div class="modal fade" id="cameraModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background:#B1083C">
                    <h5 class="modal-title"><i class="bi bi-camera-video me-2"></i>Take Photo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-2">
                    <video id="cameraFeed" autoplay playsinline muted
                           class="w-100 rounded" style="max-height:320px;background:#000"></video>
                    <canvas id="cameraCanvas" style="display:none"></canvas>
                    <div id="cameraError" class="text-danger small mt-2" style="display:none">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Camera not available or permission denied.
                    </div>
                </div>
                <div class="modal-footer justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" id="switchCamBtn"
                            title="Switch camera"><i class="bi bi-arrow-repeat"></i> Switch</button>
                    <button type="button" class="btn btn-sm text-white fw-semibold" id="captureBtn"
                            style="background:#B1083C;border-color:#B1083C">
                        <i class="bi bi-camera me-1"></i> Capture
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

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
                            <button class="btn btn-sm btn-outline-danger w-100 btn-delete-photo mt-1"
                                    style="font-size:.7rem;padding:2px 4px"
                                    data-url="{{ route('before-after-photos.destroy',$photo) }}"
                                    data-token="{{ csrf_token() }}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
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

    /* Preview thumbnails in queue */
    .preview-thumb-wrap {
        position: relative;
        width: 72px;
        height: 72px;
        flex-shrink: 0;
    }
    .preview-thumb-wrap img {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 6px;
        border: 2px solid #dee2e6;
    }
    .preview-thumb-wrap .remove-btn {
        position: absolute;
        top: -6px;
        right: -6px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #B1083C;
        color: #fff;
        border: none;
        font-size: 10px;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
    }
    .preview-thumb-wrap .cam-badge {
        position: absolute;
        bottom: 2px;
        left: 2px;
        background: rgba(0,0,0,.55);
        color: #fff;
        border-radius: 3px;
        font-size: 9px;
        padding: 1px 3px;
    }
</style>

<script>
/* ===== Queue state ===== */
let fileQueue = new DataTransfer();   // holds File objects for form submission
let previewMeta = [];                 // [{objectUrl, isCamera}] — parallel array

/* ===== Appointment → patient auto-fill ===== */
document.getElementById('apptSelect')?.addEventListener('change', function () {
    const pid = this.options[this.selectedIndex]?.dataset?.patient;
    if (pid) document.getElementById('patientIdField').value = pid;
});

/* ===== File input change ===== */
document.getElementById('photoInput')?.addEventListener('change', function () {
    for (const f of this.files) {
        fileQueue.items.add(f);
        previewMeta.push({ objectUrl: URL.createObjectURL(f), isCamera: false });
    }
    // Reset native input so same file can be re-selected after removal
    this.value = '';
    renderPreviews();
    syncInput();
});

/* ===== Render preview grid ===== */
function renderPreviews() {
    const grid   = document.getElementById('previewGrid');
    const list   = document.getElementById('previewList');
    const btn    = document.getElementById('uploadBtn');
    const badge  = document.getElementById('uploadCount');
    const status = document.getElementById('queueStatus');
    const count  = previewMeta.length;

    list.innerHTML = '';
    previewMeta.forEach((m, idx) => {
        const wrap = document.createElement('div');
        wrap.className = 'preview-thumb-wrap';

        const img = document.createElement('img');
        img.src = m.objectUrl;

        const rmBtn = document.createElement('button');
        rmBtn.className = 'remove-btn';
        rmBtn.type = 'button';
        rmBtn.innerHTML = '&times;';
        rmBtn.onclick = () => removeFromQueue(idx);

        wrap.appendChild(img);
        if (m.isCamera) {
            const badge = document.createElement('span');
            badge.className = 'cam-badge';
            badge.innerHTML = '<i class="bi bi-camera"></i>';
            wrap.appendChild(badge);
        }
        wrap.appendChild(rmBtn);
        list.appendChild(wrap);
    });

    grid.style.display = count > 0 ? 'block' : 'none';
    btn.disabled = count === 0;
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline';
        status.textContent = count + ' photo' + (count > 1 ? 's' : '') + ' ready';
    } else {
        badge.style.display = 'none';
        status.textContent = '';
    }
}

/* ===== Remove one item from queue ===== */
function removeFromQueue(idx) {
    // Rebuild DataTransfer without the removed file
    const newDT = new DataTransfer();
    const files = fileQueue.files;
    for (let i = 0; i < files.length; i++) {
        if (i !== idx) newDT.items.add(files[i]);
    }
    // Revoke removed objectUrl
    URL.revokeObjectURL(previewMeta[idx].objectUrl);
    fileQueue = newDT;
    previewMeta.splice(idx, 1);
    renderPreviews();
    syncInput();
}

/* ===== Keep the real file input in sync ===== */
function syncInput() {
    const input = document.getElementById('photoInput');
    // Assign the DataTransfer's FileList to the input
    try {
        input.files = fileQueue.files;
    } catch (e) {
        // Fallback: some older browsers; form still works because we submit via DataTransfer
    }
}

/* ===== Camera logic ===== */
let cameraStream   = null;
let useFrontCamera = true;

// Modal opened  → start camera
document.getElementById('cameraModal')?.addEventListener('show.bs.modal', async () => {
    await startCamera();
});

// Modal closed → stop camera
document.getElementById('cameraModal')?.addEventListener('hidden.bs.modal', stopCamera);

async function startCamera() {
    stopCamera();
    const errorDiv = document.getElementById('cameraError');
    errorDiv.style.display = 'none';
    const constraints = {
        video: {
            facingMode: useFrontCamera ? 'user' : 'environment',
            width:  { ideal: 1280 },
            height: { ideal: 720 }
        }
    };
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
        const video = document.getElementById('cameraFeed');
        video.srcObject = cameraStream;
        await video.play();
    } catch (err) {
        console.error('Camera error:', err);
        document.getElementById('cameraError').style.display = 'block';
    }
}

function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(t => t.stop());
        cameraStream = null;
    }
    const video = document.getElementById('cameraFeed');
    if (video) video.srcObject = null;
}

/* Switch front / rear */
document.getElementById('switchCamBtn')?.addEventListener('click', async () => {
    useFrontCamera = !useFrontCamera;
    await startCamera();
});

/* Capture snapshot */
document.getElementById('captureBtn')?.addEventListener('click', () => {
    const video  = document.getElementById('cameraFeed');
    const canvas = document.getElementById('cameraCanvas');
    if (!video.videoWidth) return;

    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    canvas.toBlob(blob => {
        const fileName = 'camera_' + Date.now() + '.jpg';
        const file = new File([blob], fileName, { type: 'image/jpeg' });
        fileQueue.items.add(file);
        previewMeta.push({ objectUrl: URL.createObjectURL(blob), isCamera: true });
        renderPreviews();
        syncInput();
    }, 'image/jpeg', 0.92);
});

/* ===== AJAX Delete with SweetAlert ===== */
$(document).on('click', '.btn-delete-photo', function () {
    const btn   = $(this);
    const url   = btn.data('url');
    const token = btn.data('token');

    Swal.fire({
        title: 'Delete this photo?',
        text: 'This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#B1083C',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Yes, delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
    }).then((result) => {
        if (!result.isConfirmed) return;

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: url,
            method: 'POST',
            data: { _method: 'DELETE', _token: token },
            success: function () {
                const card = btn.closest('.col-lg-2, .col-md-3, .col-sm-4, .col-6');
                card.fadeOut(300, function () { $(this).remove(); });
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: 'Photo removed successfully.',
                    confirmButtonColor: '#B1083C',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                });
            },
            error: function () {
                btn.prop('disabled', false).html('<i class="bi bi-trash"></i> Delete');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Could not delete the photo. Please try again.',
                    confirmButtonColor: '#B1083C',
                });
            }
        });
    });
});

/* ===== Lightbox ===== */
function showLightbox(src, caption) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxCaption').textContent = caption || '';
    document.getElementById('lightbox').style.display = 'flex';
}

/* ===== On form submit: attach queued files to form data ===== */
document.getElementById('uploadForm')?.addEventListener('submit', function (e) {
    if (fileQueue.files.length === 0) {
        e.preventDefault();
        alert('Please select or capture at least one photo.');
        return;
    }
    // Sync one final time
    syncInput();
});
</script>
@endsection
