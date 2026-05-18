@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-pencil-square me-2 text-theme-color"></i>Edit Permission</h4>
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
                <form method="POST" action="{{ route('permission.update', $permission->id) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Module <span class="text-danger">*</span></label>
                        <select name="module" class="form-select border-secondary @error('module') is-invalid @enderror">
                            <option value="">— Select Module —</option>
                            @foreach($modules as $mod)
                            <option value="{{ $mod }}" {{ old('module', $module) == $mod ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('-',' ',$mod)) }}
                            </option>
                            @endforeach
                        </select>
                        @error('module') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Action <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach(['view','create','edit','delete'] as $a)
                            <button type="button" class="btn btn-sm btn-outline-theme action-quick" data-action="{{ $a }}">{{ ucfirst($a) }}</button>
                            @endforeach
                        </div>
                        <input type="text" name="action" id="actionInput"
                               class="form-control border-secondary @error('action') is-invalid @enderror"
                               value="{{ old('action', $action) }}" required>
                        @error('action') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Update</button>
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
</style>
<script>
$('.action-quick').on('click', function () {
    $('#actionInput').val($(this).data('action'));
});
</script>
@endsection
