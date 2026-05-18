<table class="table border table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Title</th>
            <th>Short Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

        @if ($blogs->count() > 0)
            @foreach ($blogs as $blog)
                <tr>
                    <th>{{ $blog->id }}</th>
                    <td>{{ $blog->title }}</td>
                    <td>{{ $blog->short_description }}</td>
                    <td>
                        <div class="d-flex mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status" checked>
                                <label for="status" class="form-label fs-6">Draft</label>
                            </div>
                            <div class="form-check ms-1">
                                <input class="form-check-input" type="radio" name="status" id="publish">
                                <label for="publish" class="form-label fs-6">Published</label>
                            </div>
                            <div class="form-check ms-1">
                                <input class="form-check-input" type="radio" name="status" id="inactive">
                                <label for="inactive" class="form-label fs-6">Inactive</label>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex">
                            <a href="blogger/{{ $blog->id }}/edit"
                                class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2"
                                    onclick="return confirm('Are you sure to delete this Entry?')"><i
                                        class="bi bi-trash-fill"></i></button>
                            </form>
                            
                            <a href="#collapseExample" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapseExample"
                                class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                                <i class="bi bi-chat-dots-fill"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5" class="text-center">No Data Found</td>
            </tr>
        @endif
    </tbody>
</table>

{!! $blogs->withQueryString()->links('pagination::bootstrap-5') !!}
