@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-shield-lock me-2 text-theme-color"></i>Roles</h4>
            @can('roles.create')
            <a href="{{ route('role.create') }}" class="btn btn-theme btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Create Role
            </a>
            @endcan
        </div>
        <hr class="my-2">
    </div>

    @include('flash-message')

    <div class="shadow-css mx-1 mt-2 p-3">
        <div class="table-responsive">
            <table id="roles-table" class="table table-hover align-middle w-100">
                <thead class="table-theme-header">
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th>Permissions</th>
                        <th>Users</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <span class="badge-role {{ $role->name === 'super-admin' ? 'badge-superadmin' : 'badge-role-default' }}">
                                {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                            </span>
                        </td>
                        <td><span class="badge bg-theme-soft text-theme-color fw-semibold">{{ $role->permissions_count }}</span></td>
                        <td><span class="badge bg-secondary">{{ $role->users_count }}</span></td>
                        <td>
                            @can('roles.edit')
                            <a href="{{ route('role.edit', $role->id) }}" class="btn btn-sm btn-outline-theme me-1">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            @endcan
                            @can('roles.delete')
                            @if($role->name !== 'super-admin')
                            <form action="{{ route('role.destroy', $role->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete role {{ $role->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash3"></i> Delete
                                </button>
                            </form>
                            @endif
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<style>
    .text-theme-color { color: #B1083C; }
    .bg-theme-soft { background: #fce4ec; }
    .btn-theme { background: linear-gradient(90deg,#B1083C,#d13729); color:#fff; border:none; }
    .btn-theme:hover { opacity:.9; color:#fff; }
    .btn-outline-theme { border-color:#B1083C; color:#B1083C; }
    .btn-outline-theme:hover { background:#B1083C; color:#fff; }
    .table-theme-header th { background: linear-gradient(90deg,#B1083C 0%,#d13729 100%); color:#fff; border:none; }
    .badge-role { padding:5px 12px; border-radius:20px; font-size:13px; font-weight:600; }
    .badge-superadmin { background:#fce4ec; color:#B1083C; border:1px solid #B1083C; }
    .badge-role-default { background:#f0f0f0; color:#444; border:1px solid #ccc; }
    .shadow-css { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.08); }
</style>
<script>
    $(document).ready(function () {
        $('#roles-table').DataTable({
            responsive: true,
            pageLength: 15,
            order: [[0, 'asc']],
            columnDefs: [{ orderable: false, targets: [4] }],
            language: { search: '<i class="bi bi-search"></i>', searchPlaceholder: 'Search roles...' }
        });
    });
</script>
@endsection
