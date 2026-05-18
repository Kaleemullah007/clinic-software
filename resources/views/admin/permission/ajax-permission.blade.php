<table class="table border table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

        {{-- @if($permissions->count()>0)

        @foreach ($permissions as $permission )


        <tr>
            <th>{{$permission->id}}</th>
        <td>{{$permission->name}}</td>
        <td>{{$permission->status}}</td>
        <td>
            <div class="d-flex">
                <a href="permissions/{{$permission->id}}/edit" class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                    <i class="bi bi-pencil"></i>
                </a>
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
            <td colspan="4" class="text-center">No Data Found</td>
        </tr>
        @endif --}}

    </tbody>
</table>
{{-- {!! $permissions->withQueryString()->links('pagination::bootstrap-5') !!} --}}