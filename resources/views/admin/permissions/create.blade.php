@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-key me-2 text-theme-color"></i>Add Permission</h4>
            <a href="{{ route('permission.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
        <hr class="my-2">
    </div>

    <div class="row mx-1 mt-2">
        <div class="col-lg-6 col-12">
            <div class="shadow-css p-4">
                @include('flash-message')
                <form method="POST" action="{{ route('permission.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Module <span class="text-danger">*</span></label>
                        <select name="module" class="form-select border-secondary @error('module') is-invalid @enderror" id="moduleSelect">
                            <option value="">— Select Module —</option>
                            @foreach($modules as $mod)
                            <option value="{{ $mod }}" {{ old('module') == $mod ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('-',' ',$mod)) }}
                            </option>
                            @endforeach
                            <option value="__custom__">+ Custom module</option>
                        </select>
                        @error('module') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div id="customModuleWrap" class="mb-3 d-none">
                        <label class="form-label fw-semibold">Custom Module Name</label>
                        <input type="text" id="customModule" class="form-control border-secondary"
                               placeholder="e.g. reports">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Action <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach(['view','create','edit','delete'] as $a)
                            <button type="button" class="btn btn-sm btn-outline-theme action-quick"
                                    data-action="{{ $a }}">{{ ucfirst($a) }}</button>
                            @endforeach
                        </div>
                        <input type="text" name="action" id="actionInput"
                               class="form-control border-secondary @error('action') is-invalid @enderror"
                               value="{{ old('action') }}" placeholder="e.g. export" required>
                        @error('action') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Preview</label>
                        <div class="perm-preview" id="permPreview">—</div>
                    </div>

                    <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Save Permission</button>
                    <a href="{{ route('permission.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<style>
    .text-theme-color { color:#B1083C; }
    .btn-theme { background:linear-gradient(90deg,#B1083C,#d13729); color:#fff; border:none; }
    .btn-outline-theme { border-color:#B1083C; color:#B1083C; }
    .btn-outline-theme:hover { background:#B1083C; color:#fff; }
    .shadow-css { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.08); }
    .perm-preview { background:#f8f8f8; border:1px dashed #ccc; border-radius:6px; padding:8px 14px; font-family:monospace; font-size:14px; color:#B1083C; }
</style>
<script>
$(document).ready(function () {
    function updatePreview() {
        let mod    = $('#moduleSelect').val() === '__custom__' ? $('#customModule').val() : $('#moduleSelect').val();
        let action = $('#actionInput').val();
        let text   = (mod && action) ? `${mod}.${action}` : '—';
        $('#permPreview').text(text);
    }

    $('#moduleSelect').on('change', function () {
        if ($(this).val() === '__custom__') {
            $('#customModuleWrap').removeClass('d-none');
        } else {
            $('#customModuleWrap').addClass('d-none');
        }
        updatePreview();
    });

    $('#customModule').on('input', updatePreview);
    $('#actionInput').on('input', updatePreview);

    // When custom module is typed, update the real module field via form submit
    $('form').on('submit', function () {
        if ($('#moduleSelect').val() === '__custom__') {
            $('#moduleSelect').val($('#customModule').val());
        }
    });

    $('.action-quick').on('click', function () {
        $('#actionInput').val($(this).data('action'));
        updatePreview();
    });
});
</script>
@endsection
