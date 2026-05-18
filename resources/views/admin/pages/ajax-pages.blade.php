<table class="table border table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Heading</th>
            <th>Title</th>
            <th>Category Name</th>
            <th>Is_Discounted</th>
            <th>Price</th>
            <th>Discounted_Price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

    @if($pages->count()>0)
    @foreach($pages as $page)

        <tr>
            <th>{{$page->id}}</th>
            <td>{{$page->heading}}</td>
            <td>{{$page->title}}</td>

            <td>{{$page->category->name}}</td>
            <td>
                <input type="checkbox" data-toggle="switchbutton" data-size="sm" data-onstyle="success"
                data-offstyle="danger" data-onlabel="Yes" data-offlabel="No" @if($page->is_discounted == 1) checked @endif>
            </td>
            <td>{{$page->price}}</td>
            <td>{{$page->discounted_price}}</td>
            <td>
                <input type="checkbox" data-toggle="switchbutton" data-size="sm" data-onstyle="success"
                data-offstyle="danger" data-onlabel="Active" data-offlabel="Inactive" @if($page->status == 1) checked @endif>
            </td>
            <td>
                <div class="d-flex">
                    {{-- <a href="#"
                        class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                        <i class="bi bi-eye-fill"></i>
                    </a> --}}
                    <a  href="pages/{{$page->id}}/edit"
                            class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('pages.destroy', $page->id ) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark" onclick="return confirm('Are you sure to delete this Entry?')"><i class="bi bi-trash-fill"></i></button>
                    </form>
                </div>
            </td>
        </tr>
    @endforeach
    @else
        <tr>
            <td colspan="9" class="text-center">No Data Found</td>
        </tr>
    @endif
    </tbody>
</table>

{!! $pages->withQueryString()->links('pagination::bootstrap-5') !!}