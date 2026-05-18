<table class="table border table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Permissions</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

        {{-- @if($modules->count()>0)

        @foreach ($modules as $module )


        <tr>
            <th>{{$module->id}}</th>
        <td>{{$module->name}}</td>
        <td>{{$module->permissions}}</td>
        <td>
            <div class="d-flex">
                <a href="modules/{{$module->id}}/edit" class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
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
{{-- {!! $modules->withQueryString()->links('pagination::bootstrap-5') !!} --}}