@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="row pt-3 mx-1 align-items-center mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('whatsapp-campaign.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-file-earmark-text me-2" style="color:#B1083C"></i>Campaign Templates
                </h4>
            </div>
            @can('whatsapp-campaign.templates')
            <a href="{{ route('whatsapp-campaign.templates.create') }}" class="btn btn-sm btn-danger">
                <i class="bi bi-plus-circle me-1"></i>New Template
            </a>
            @endcan
        </div>
        <hr class="mt-2 mb-0">
    </div>

    <div class="mx-1 shadow-css p-3">
        @include('flash-message')
        <table id="templatesTable" class="table table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Template</th>
                    <th>Type</th>
                    <th>Preview</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
        </table>
    </div>

</div>

{{-- Image Preview Modal --}}
<div class="modal fade" id="imgPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a1a2e;color:#fff">
                <h5 class="modal-title" id="imgPreviewTitle"><i class="bi bi-image me-2"></i>Image Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="imgPreviewSrc" src="" alt="" class="img-fluid rounded" style="max-height:70vh">
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@include('admin.whatsapp-campaign._styles')
<script>
$(window).on('load', function () {
    var table = $('#templatesTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("whatsapp-campaign.templates") }}',
        columns: [
            { data: 'DT_RowIndex',  name: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
            { data: 'name_col',     name: 'name' },
            { data: 'type_col',     name: 'message_type', orderable: false, width: '130px' },
            { data: 'preview_col',  name: 'preview_col',  orderable: false, searchable: false },
            { data: 'status_col',   name: 'status',       orderable: false, width: '90px' },
            { data: 'created_at',   name: 'created_at',   render: d => d ? new Date(d).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}) : '—' },
            { data: 'action',       name: 'action',       orderable: false, searchable: false, className: 'text-center' },
        ],
        order: [[5, 'desc']],
        responsive: true,
        pageLength: 15,
        language: { searchPlaceholder: 'Search templates…', processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Loading…' },
    });

    // Image preview
    $(document).on('click', '.btn-preview-img', function () {
        const img  = $(this).data('img');
        const name = $(this).data('name');
        $('#imgPreviewTitle').html('<i class="bi bi-image me-2"></i>' + name);
        $('#imgPreviewSrc').attr('src', img);
        $('#imgPreviewModal').modal('show');
    });

    // Delete template
    $(document).on('click', '.btn-delete-template', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Delete Template?',
            text: 'This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#B1083C',
            confirmButtonText: 'Delete',
        }).then(function (r) {
            if (!r.isConfirmed) return;
            $.ajax({
                url: '{{ url("whatsapp-campaign/templates") }}/' + id,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function () { table.ajax.reload(); }
            });
        });
    });
});
</script>
@endsection
