<table class="table border table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Clinic</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

     @if($users->count()>0)
     @foreach($users as $user)
        <tr>
            <td>{{$user->id}}</td>
            <td>{{$user->name}}</td>
            <td>{{$user->email}}</td>
            <td>{{$user->role}}</td>
            <td>{{$user->clinic}}</td>

            <td>
                <input type="checkbox" data-toggle="switchbutton" data-size="sm" data-onstyle="success"
                data-offstyle="danger" data-onlabel="Active" data-offlabel="Inactive"  @if($user->status == 1) checked @endif>
            </td>
            <td>
                <div class="d-flex">
                    {{-- <a href="#"
                        class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                        <i class="bi bi-eye-fill"></i>
                    </a> --}}
                    <a href="users/{{$user->id}}/edit"
                            class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('users.destroy', $user->id ) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark" onclick="return confirm('Are you sure to delete this record?')"><i class="bi bi-trash-fill"></i></button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    @else
        <tr>
            <td colspan="6" class="text-center">No Data Found</td>
        </tr>
    @endif
    </tbody>
</table>
{!! $users->withQueryString()->links('pagination::bootstrap-5') !!}