@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-person-plus me-2 text-theme-color"></i>Create User</h4>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
        <hr class="my-2">
    </div>

    @include('flash-message')

    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="row mx-1 g-3">

            {{-- Basic Info --}}
            <div class="col-12">
                <div class="shadow-css p-3">
                    <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-person me-2 text-theme-color"></i>Basic Information</h6>
                    <div class="row g-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control border-secondary @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Full Name" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control border-secondary @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="email@example.com" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control border-secondary @error('password') is-invalid @enderror"
                                   placeholder="Minimum 6 characters" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control border-secondary"
                                   value="{{ old('phone') }}" placeholder="Phone number">
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label fw-semibold">Status</label><br>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="statusToggle" role="switch" checked>
                                <label class="form-check-label" for="statusToggle">Active</label>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label fw-semibold">Clinic</label>
                            <select name="clinic_id" class="form-select border-secondary @error('clinic_id') is-invalid @enderror">
                                <option value="">— No Clinic —</option>
                                @foreach($clinics as $clinic)
                                    <option value="{{ $clinic->id }}" {{ old('clinic_id') == $clinic->id ? 'selected' : '' }}>
                                        {{ $clinic->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('clinic_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Location --}}
            <div class="col-12">
                <div class="shadow-css p-3">
                    <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-geo-alt me-2 text-theme-color"></i>Location</h6>
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label fw-semibold">Country</label>
                            <select name="country_id" id="countrySelect" class="form-select border-secondary">
                                <option value="">— Select Country —</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label fw-semibold">State / Province</label>
                            <select name="state_id" id="stateSelect" class="form-select border-secondary" disabled>
                                <option value="">— Select State —</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label fw-semibold">City</label>
                            <select name="city_id" id="citySelect" class="form-select border-secondary" disabled>
                                <option value="">— Select City —</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Role Assignment --}}
            <div class="col-12">
                <div class="shadow-css p-3">
                    <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-shield me-2 text-theme-color"></i>Assign Role</h6>
                    <div class="row g-3">
                        @foreach($roles as $role)
                        <div class="col-lg-3 col-md-4 col-6">
                            <div class="role-option" onclick="selectRole(this, '{{ $role->name }}')">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-{{ $role->name === 'super-admin' ? 'fill text-danger' : 'half text-secondary' }} fs-5"></i>
                                    <div>
                                        <div class="fw-semibold" style="font-size:13px">{{ ucfirst(str_replace('-',' ',$role->name)) }}</div>
                                        <small class="text-muted">{{ $role->permissions->count() }} permissions</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="spatie_role" id="spatieRole" value="">
                    <small class="text-muted mt-2 d-block">
                        <i class="bi bi-info-circle me-1"></i>
                        Selecting a role automatically gives the user all permissions of that role.
                    </small>
                </div>
            </div>

            {{-- Direct Permissions --}}
            <div class="col-12">
                <div class="shadow-css p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-key me-2 text-theme-color"></i>Direct Permissions
                            <small class="text-muted fw-normal">(extra, on top of role)</small>
                        </h6>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-theme me-1" id="selectAllDirect">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllDirect">Deselect All</button>
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
                                               data-module="{{ $module }}" role="switch">
                                    </div>
                                </div>
                                <div class="module-body">
                                    @foreach($actions as $action)
                                    @php $permName = "{$module}.{$action}"; @endphp
                                    <div class="d-flex justify-content-between align-items-center perm-row">
                                        <label class="perm-label text-capitalize" for="dp_{{ str_replace(['.', '-'], '_', $permName) }}">
                                            <i class="bi bi-{{ $action === 'view' ? 'eye' : ($action === 'create' ? 'plus-circle' : ($action === 'edit' ? 'pencil' : 'trash3')) }} me-1"></i>
                                            {{ ucfirst($action) }}
                                        </label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input perm-toggle perm-{{ $module }}"
                                                   type="checkbox"
                                                   name="direct_permissions[]"
                                                   value="{{ $permName }}"
                                                   id="dp_{{ str_replace(['.', '-'], '_', $permName) }}"
                                                   role="switch">
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
                    <button type="submit" class="btn btn-theme"><i class="bi bi-save me-1"></i> Create User</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary ms-2"><i class="bi bi-x-circle me-1"></i> Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
@include('admin.roles.partials.toggle-styles')
<style>
    .role-option { border:2px solid #e0e0e0; border-radius:10px; padding:12px 14px; cursor:pointer; transition:.2s; }
    .role-option:hover { border-color:#B1083C; background:#fff5f7; }
    .role-selected { border-color:#B1083C !important; background:#fce4ec !important; }
</style>
<script>
/* ── Cascading location dropdowns ── */
(function () {
    const statesUrl  = '{{ route("pos.states") }}';
    const citiesUrl  = '{{ route("pos.cities") }}';

    const countryEl = document.getElementById('countrySelect');
    const stateEl   = document.getElementById('stateSelect');
    const cityEl    = document.getElementById('citySelect');

    function resetSelect(el, placeholder) {
        el.innerHTML = `<option value="">${placeholder}</option>`;
        el.disabled  = true;
    }

    countryEl.addEventListener('change', function () {
        resetSelect(stateEl, '— Select State —');
        resetSelect(cityEl,  '— Select City —');
        if (!this.value) return;

        fetch(statesUrl + '?country_id=' + this.value)
            .then(r => r.json())
            .then(data => {
                data.forEach(s => {
                    stateEl.insertAdjacentHTML('beforeend',
                        `<option value="${s.id}">${s.name}</option>`);
                });
                stateEl.disabled = data.length === 0;
            });
    });

    stateEl.addEventListener('change', function () {
        resetSelect(cityEl, '— Select City —');
        if (!this.value) return;

        fetch(citiesUrl + '?state_id=' + this.value)
            .then(r => r.json())
            .then(data => {
                data.forEach(c => {
                    cityEl.insertAdjacentHTML('beforeend',
                        `<option value="${c.id}">${c.name}</option>`);
                });
                cityEl.disabled = data.length === 0;
            });
    });

    /* Restore old() values after validation failure */
    @if(old('country_id'))
    countryEl.value = '{{ old("country_id") }}';
    fetch(statesUrl + '?country_id={{ old("country_id") }}')
        .then(r => r.json())
        .then(data => {
            data.forEach(s => stateEl.insertAdjacentHTML('beforeend',
                `<option value="${s.id}"${s.id == '{{ old("state_id") }}' ? ' selected' : ''}>${s.name}</option>`));
            stateEl.disabled = false;
            @if(old('state_id'))
            return fetch(citiesUrl + '?state_id={{ old("state_id") }}');
            @endif
        })
        @if(old('state_id'))
        .then(r => r && r.json())
        .then(data => {
            if (!data) return;
            data.forEach(c => cityEl.insertAdjacentHTML('beforeend',
                `<option value="${c.id}"${c.id == '{{ old("city_id") }}' ? ' selected' : ''}>${c.name}</option>`));
            cityEl.disabled = false;
        })
        @endif
        .catch(() => {});
    @endif
})();

function selectRole(el, roleName) {
    document.querySelectorAll('.role-option').forEach(r => r.classList.remove('role-selected'));
    el.classList.add('role-selected');
    document.getElementById('spatieRole').value = roleName;
}
$(document).ready(function () {
    $('.module-toggle').on('change', function () {
        const mod = $(this).data('module');
        $(`.perm-${mod.replace(/-/g,'\\-')}`).prop('checked', $(this).is(':checked'));
    });
    $('.perm-toggle').on('change', function () {
        const classes = $(this).attr('class').match(/perm-([\w-]+)/g) || [];
        classes.forEach(cls => {
            const mod = cls.replace('perm-','');
            if (mod === 'toggle') return;
            const escaped = mod.replace(/-/g,'\\-');
            const total   = $(`.perm-${escaped}`).length;
            const checked = $(`.perm-${escaped}:checked`).length;
            $(`[data-module="${mod}"]`).prop('checked', total === checked);
        });
    });
    $('#selectAllDirect').on('click', function () { $('.perm-toggle,.module-toggle').prop('checked',true); });
    $('#deselectAllDirect').on('click', function () { $('.perm-toggle,.module-toggle').prop('checked',false); });
});
</script>
@endsection
