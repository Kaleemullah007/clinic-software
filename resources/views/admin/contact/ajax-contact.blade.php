<table class="table border table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Reply</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        @if($contacts->count()>0)
        @foreach ($contacts as $contact )

        <tr>
            <th>{{$contact->id}}</th>
            <td>{{$contact->name}}</td>
            <td>{{$contact->email}}</td>
            <td>{{$contact->phone}}</td>
            <td>{{$contact->subject}}</td>
            <td>{{$contact->message}}</td>
            <td>
                <button type="button" data-bs-toggle="modal" data-bs-target="#replyContact" class="btn btn-success">Reply</button>
            </td>
            <td>
                <button type="button" data-bs-toggle="modal" data-bs-target="#detailsContact" class="btn btn-success">Details</button>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="8" class="text-center">No Data Found</td>
        </tr>
        @endif
    </tbody>
</table>

{!! $contacts->withQueryString()->links('pagination::bootstrap-5') !!}