@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-people me-2 text-theme-color"></i>Users</h4>
            @can('users.create')
            <a href="{{ route('users.create') }}" class="btn btn-theme btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Create User
            </a>
            @endcan
        </div>
        <hr class="my-2">
    </div>

    @include('flash-message')

    <div class="shadow-css mx-1 mt-2 p-3">
        <div class="table-responsive">
            <table id="users-table" class="table table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<style>
    .text-theme-color { color:#B1083C; }
    .btn-theme { background:linear-gradient(90deg,#B1083C,#d13729); color:#fff; border:none; }
    .btn-theme:hover { opacity:.9; color:#fff; }
    .btn-outline-theme { border-color:#B1083C; color:#B1083C; }
    .btn-outline-theme:hover { background:#B1083C; color:#fff; }
    .shadow-css { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.08); }
    #users-table thead th { background:linear-gradient(90deg,#B1083C 0%,#d13729 100%); color:#fff; border:none; }
</style>
<script>
var usersTable;
$(function () {
    usersTable = $('#users-table').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("users.index") }}',
        columns: [
            { data: 'DT_RowIndex',   name: 'DT_RowIndex',   orderable: false, searchable: false, width: '50px' },
            { data: 'name',          name: 'name' },
            { data: 'email',         name: 'email' },
            { data: 'roles_html',    name: 'roles_html',    orderable: false, searchable: false },
            { data: 'status_badge',  name: 'status_badge',  orderable: false, searchable: false },
            { data: 'action',        name: 'action',        orderable: false, searchable: false, className: 'text-center' },
        ],
        responsive: true,
        pageLength: 15,
        language: { searchPlaceholder: 'Search users…', processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Loading…' },
    });
});

// ── Status toggle with SweetAlert2 confirmation ────────────────────────────
$(document).on('click', '.btn-toggle-status', function () {
    var btn    = $(this);
    var id     = btn.data('id');
    var name   = btn.data('name');
    var active = parseInt(btn.data('status'));
    var url    = btn.data('url');
    var action = active ? 'Deactivate' : 'Activate';
    var icon   = active ? 'warning' : 'question';
    var color  = active ? '#ef4444'  : '#10b981';

    Swal.fire({
        title: action + ' user?',
        html: 'Are you sure you want to <strong>' + action.toLowerCase() + '</strong> <strong>' + name + '</strong>?',
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: color,
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-toggle-' + (active ? 'off' : 'on') + ' me-1"></i>' + action,
        cancelButtonText: 'Cancel',
    }).then(function (result) {
        if (!result.isConfirmed) return;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: data.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                });
                usersTable.ajax.reload(null, false);
            }
        })
        .catch(() => Swal.fire({ icon:'error', title:'Request failed.', toast:true, position:'top-end', showConfirmButton:false, timer:2500 }));
    });
});
</script>
@endsection
