<table class="table border table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @if($categories->count()>0)
    <?php
     if(request('page')>1)

        $counter = ((request('page')-1)*10) +1;
        else
        $counter = 1;
     ?>   
    @foreach($categories as $category)
        <tr>
            <th>{{$counter++}}</th>
            <td>{{$category->name}}</td>
            <td>{{auth()->user()->currency}} {{number_format($category->price,2)}}</td>
            <!--<td>{{$category->ParentCategory->name ?? ''}}</td>-->
            <td>
                <input type="checkbox"  data-toggle="toggle"   data-size="sm" data-onstyle="success"
                data-offstyle="danger" data-onlabel="Active" data-offlabel="Inactive" @if($category->status == 1) checked @endif >
            </td>
            <td>
                <div class="d-flex">
                    {{-- <a href="#"
                        class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                        <i class="bi bi-eye-fill"></i>
                    </a> --}}
                    <a href="category/{{$category->id}}/edit"
                            class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('category.destroy', $category->id ) }}" method="post">
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
            <td colspan="5" class="text-center">No Data Found</td>
        </tr>
    @endif
    </tbody>
</table>

{!! $categories->withQueryString()->links('pagination::bootstrap-5') !!}
