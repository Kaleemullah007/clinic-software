@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-key me-2 text-theme-color"></i>Permissions</h4>
            @can('permissions.create')
            <a href="{{ route('permission.create') }}" class="btn btn-theme btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Add Permission
            </a>
            @endcan
        </div>
        <hr class="my-2">
    </div>

    @include('flash-message')

    <div class="row mx-1 g-3 mt-1">
        @foreach($permissions as $module => $perms)
        <div class="col-lg-4 col-md-6 col-12">
            <div class="module-perm-card">
                <div class="module-perm-header">
                    <i class="bi bi-layers me-1"></i>
                    {{ ucfirst(str_replace('-', ' ', $module)) }}
                    <span class="badge bg-white text-theme-color ms-2">{{ $perms->count() }}</span>
                </div>
                <div class="module-perm-body">
                    @foreach($perms as $perm)
                    @php $action = explode('.', $perm->name)[1] ?? $perm->name; @endphp
                    <div class="perm-item d-flex justify-content-between align-items-center">
                        <span>
                            <i class="bi bi-{{ $action === 'view' ? 'eye' : ($action === 'create' ? 'plus-circle' : ($action === 'edit' ? 'pencil' : ($action === 'delete' ? 'trash3' : 'dot'))) }} me-1 text-muted"></i>
                            <code class="perm-name">{{ $perm->name }}</code>
                        </span>
                        <div class="d-flex gap-1">
                            @can('permissions.edit')
                            <a href="{{ route('permission.edit', $perm->id) }}" class="btn btn-xs btn-outline-theme">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('permissions.delete')
                            <form action="{{ route('permission.destroy', $perm->id) }}" method="POST"
                                  onsubmit="return confirm('Delete permission {{ $perm->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-outline-danger">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('script')
<style>
    .text-theme-color { color: #B1083C; }
    .btn-theme { background: linear-gradient(90deg,#B1083C,#d13729); color:#fff; border:none; }
    .btn-theme:hover { opacity:.9; color:#fff; }
    .btn-outline-theme { border-color:#B1083C; color:#B1083C; }
    .btn-outline-theme:hover { background:#B1083C; color:#fff; }

    .module-perm-card { border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); height:100%; }
    .module-perm-header {
        background: linear-gradient(90deg,#B1083C 0%,#d13729 100%);
        color:#fff; padding:10px 15px; font-size:14px; font-weight:700;
    }
    .module-perm-body { background:#fff; padding:6px 12px; }
    .perm-item { padding:7px 2px; border-bottom:1px solid #f5f5f5; }
    .perm-item:last-child { border-bottom:none; }
    .perm-name { font-size:12.5px; color:#333; background:#f8f8f8; padding:2px 6px; border-radius:4px; }
    .btn-xs { padding:2px 8px; font-size:12px; }
</style>
@endsection
