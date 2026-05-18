@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-shield-plus me-2 text-theme-color"></i>Create Role</h4>
            <a href="{{ route('role.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
        <hr class="my-2">
    </div>

    @include('flash-message')

    <form method="POST" action="{{ route('role.store') }}">
        @csrf
        <div class="row mx-1">
            {{-- Role Name --}}
            <div class="col-12 mb-3">
                <div class="shadow-css p-3">
                    <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control border-secondary @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="e.g. receptionist" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Permissions by Module --}}
            <div class="col-12 mb-3">
                <div class="shadow-css p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-key me-2 text-theme-color"></i>Assign Permissions</h6>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-theme me-1" id="selectAll">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">Deselect All</button>
                        </div>
                    </div>

                    <div class="row g-3">
                        @foreach($modules as $module => $actions)
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="module-card">
                                <div class="module-header d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-capitalize">
                                        <i class="bi bi-layers me-1"></i>{{ str_replace('-', ' ', $module) }}
                                    </span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input module-toggle" type="checkbox"
                                               data-module="{{ $module }}" role="switch" title="Toggle all {{ $module }}">
                                    </div>
                                </div>
                                <div class="module-body">
                                    @foreach($actions as $action)
                                    @php $permName = "{$module}.{$action}"; @endphp
                                    <div class="d-flex justify-content-between align-items-center perm-row">
                                        <label class="perm-label text-capitalize" for="perm_{{ str_replace(['.', '-'], '_', $permName) }}">
                                            <i class="bi bi-{{ $action === 'view' ? 'eye' : ($action === 'create' ? 'plus-circle' : ($action === 'edit' ? 'pencil' : 'trash3')) }} me-1"></i>
                                            {{ ucfirst($action) }}
                                        </label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input perm-toggle perm-{{ $module }}"
                                                   type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permName }}"
                                                   id="perm_{{ str_replace(['.', '-'], '_', $permName) }}"
                                                   role="switch"
                                                   {{ old('permissions') && in_array($permName, old('permissions', [])) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="col-12 mb-4">
                <div class="shadow-css p-3">
                    <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Save Role</button>
                    <a href="{{ route('role.index') }}" class="btn btn-secondary ms-2"><i class="bi bi-x-circle me-1"></i> Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
@include('admin.roles.partials.toggle-styles')
<script>
$(document).ready(function () {
    // Module-level toggle
    $('.module-toggle').on('change', function () {
        const module = $(this).data('module');
        const checked = $(this).is(':checked');
        $(`.perm-${module.replace('-','\\-')}`).prop('checked', checked);
    });

    // Sync module toggle when individual perms change
    $('.perm-toggle').on('change', function () {
        const classes = $(this).attr('class').match(/perm-([^\s]+)/g);
        if (!classes) return;
        classes.forEach(cls => {
            const mod = cls.replace('perm-', '');
            if (mod === 'toggle') return;
            const total   = $(`.perm-${mod.replace('-','\\-')}`).length;
            const checked = $(`.perm-${mod.replace('-','\\-')}:checked`).length;
            $(`[data-module="${mod}"]`).prop('checked', total === checked);
        });
    });

    // Select / Deselect all
    $('#selectAll').on('click', function () {
        $('.perm-toggle, .module-toggle').prop('checked', true);
    });
    $('#deselectAll').on('click', function () {
        $('.perm-toggle, .module-toggle').prop('checked', false);
    });
});
</script>
@endsection
