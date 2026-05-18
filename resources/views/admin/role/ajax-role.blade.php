<table class="table border table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

    {{-- @if($roles->count()>0)

        @foreach ($roles as $role )


        <tr>
            <th>{{$role->id}}</th>
            <td>{{$role->name}}</td>
            <td>{{$role->Description}}</td>
            <td>{{$role->status}}</td>
            <td>
                <div class="d-flex">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#editrole"
                            class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark" onclick="return confirm('Are you sure to delete this Entry?')"><i class="bi bi-trash-fill"></i></button>
                    </form>
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
    @else
        <tr>
            <td colspan="9" class="text-center">No Data Found</td>
        </tr>
    @endif --}}

    </tbody>
</table>
{{-- {!! $roles->withQueryString()->links('pagination::bootstrap-5') !!} --}}
