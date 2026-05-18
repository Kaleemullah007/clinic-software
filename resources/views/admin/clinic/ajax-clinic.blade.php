<table class="table border table-striped table-hover align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Support Email</th>
            <th>Notification Email</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($clinics as $clinic)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td class="fw-semibold">{{ $clinic->name }}</td>
            <td>{{ $clinic->phone }}</td>
            <td>{{ $clinic->address }}</td>
            <td>{{ $clinic->support_email }}</td>
            <td>{{ $clinic->notification_email }}</td>
            <td>
                @if($clinic->status)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-danger">Inactive</span>
                @endif
            </td>
            <td>
                <div class="d-flex gap-1">
                    @can('update', $clinic)
                    <a href="{{ route('clinic.edit', $clinic->id) }}"
                       class="btn btn-sm btn-outline-secondary" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    @endcan
                    @can('delete', $clinic)
                    <form action="{{ route('clinic.destroy', $clinic->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this clinic?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center text-muted py-4">No clinics found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
