<div class="collapse" id="collapseExample">
    <div class="container border ">
        <div class="row pt-3">
            <div class="col-12">
                <h4>Comments</h4>
            </div>
            <hr>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Comment</th>
                        <th>Date and Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                            <tr>
                                <th>001</th>
                                <td>Ali Khan</td>
                                <td>Very helpful and good content.</td>
                                <td>12-07-2023  15:25:41</td>
                                <td>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-sm me-2 btn-success">
                                            <i class="bi bi-check-lg me-1"></i>
                                            Approve</button>
                                            <button type="button" class="btn btn-sm me-2 btn-danger">
                                                <i class="bi bi-x-lg me-1"></i>
                                                Reject</button>
                                        {{-- <a href="blogs/{{ $blog->id }}/edit"
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
                                        </form> --}}

                                        {{-- <a href="#collapseExample" data-bs-toggle="collapse" role="button"
                                            aria-expanded="false" aria-controls="collapseExample"
                                            class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                                            <i class="bi bi-chat-dots-fill"></i>
                                        </a> --}}
                                    </div>
                                </td>
                            </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
