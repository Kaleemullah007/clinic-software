<table class="table border table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

    {{-- @if($placeholders->count()>0)

        @foreach ($placeholders as $placeholder )


        <tr>
            <th>{{$placeholder->id}}</th>
            <td>{{$placeholder->name}}</td>
            <td>
                <div class="d-flex">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#editPlaceholder"
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
{{-- {!! $placeholders->withQueryString()->links('pagination::bootstrap-5') !!} --}}
