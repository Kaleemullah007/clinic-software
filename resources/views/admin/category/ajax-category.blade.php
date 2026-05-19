<table class="table border table-striped table-hover align-middle" id="servicesTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Price</th>
            <th>Total Services</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @forelse($categories as $category)
        <tr>
            <th>{{ $loop->iteration }}</th>
            <td>{{$category->name}}</td>
            <td>{{auth()->user()->currency}} {{number_format($category->price,2)}}</td>
            <!--<td>{{$category->ParentCategory->name ?? ''}}</td>-->
            <td data-order="{{ $category->appointment_services_count ?? 0 }}">
                <span class="badge rounded-pill"
                      style="background:#B1083C; font-size:.82rem; padding:4px 10px">
                    {{ $category->appointment_services_count ?? 0 }}
                </span>
            </td>
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
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">No services found.</td>
        </tr>
    @endforelse
    </tbody>
</table>
