<table class="table border table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Placeholder</th>
            <th>Subject</th>
            <th>Short Description</th>
            <th>Long Description</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>001</th>
            <td>Name 1</td>
            <td>placeholder 1</td>
            <td>fgdsdgfsd</td>
            <td>dgfsgdf gsddsfds</td>
            <td>dfsgdfgfdsds gd dgs dsds gdg d ss fg dhfg</td>
            <td>
                <div class="d-flex">
                    <a
                    href="{{ route('email.edit',['id'=>1]) }}"
                     class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                        <i class="bi bi-pencil"></i>
                    </a>
                    {{-- <form action="" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark"
                            onclick="return confirm('Are you sure to delete this Entry?')">
                            <i class="bi bi-trash-fill"></i></button>
                    </form> --}}
                </div>
            </td>
        </tr>
    </tbody>
</table>
