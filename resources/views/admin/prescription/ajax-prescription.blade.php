<table class="table border table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Remarks</th>
            <th>Medicine</th>
            <th>Appointment Id</th>
            <th>User Id</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if($prescriptions->count()>0)
        @foreach ($prescriptions as $prescription )
            
        <tr>    
            <th>{{$prescription->id}}</th>
            <td>{{$prescription->remarks}}</td>
            <td>{{$prescription->medicine}}</td>
            <td>{{$prescription->appointment_id}}</td>
            <td>{{$prescription->user_id}}</td>
            <td>
                <div class="d-flex">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#editPrescription"
                            class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                        <i class="bi bi-pencil"></i>
                    </button>
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

{!! $prescriptions->withQueryString()->links('pagination::bootstrap-5') !!}
