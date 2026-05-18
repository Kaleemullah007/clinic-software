<table class="table border table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Price</th>
            <th>Paid Amount</th>
            <th>Discount</th>
            <th>Service</th>
            <th>Phone</th>
            <th>Date</th>
            <th>Time</th>
            <!--<th>Appointment Status</th>-->
            <th>Is Paid</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

    @if($appointments->count()>0)

 <?php
     if(request('page')>1)

        $counter = ((request('page')-1)*10) +1;
        else
        $counter = 1;
     ?>
     
        @foreach ($appointments as $appointment )


        <tr>
            <th>{{$counter++}}</th>
            <td>{{$appointment->name}}</td>
            <td>{{$appointment->price}}</td>
            <td>{{$appointment->discounted_price - $appointment->discount??0}}</td>
            <td>{{$appointment->discount??'0.00'}}</td>
            <td>
             @foreach($appointment->appointmentService as $service)
             <p>{{$service->name}} RS.{{$service->discounted_price}}</p>

             @endforeach
            </td>
            <!--$appointment->phone-->
            <td>{{$appointment->phone}}</td>
            <td>{{$appointment->date}}</td>
            <td>{{$appointment->time}}</td>
            <!--<td><span class="badge  @if($appointment->appointment_status == 5) text-bg-success @else text-bg-danger  @endif  px-2 py-1">{{appointmentStaus($appointment->appointment_status)}}</span></td>-->
            <td>
                <input type="checkbox" name="is_paid" id="is_paid" data-toggle="toggle"  checked data-size="sm" data-onstyle="success"
                data-offstyle="danger" data-onlabel="Paid" data-offlabel="Unpaid" @if($appointment->status == 1) checked @endif>

            </td>
            <td>
                <div class="d-flex">
                    <a type="button" href="appointments/{{$appointment->id}}/edit"
                            class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                        <i class="bi bi-pencil"></i>
                    </a>
                    {{-- <button type="button" data-bs-toggle="modal" data-bs-target="#editAppointment"
                            class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                        <i class="bi bi-pencil"></i>
                    </button> --}}
                    {{-- <form action="" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark" onclick="return confirm('Are you sure to delete this Entry?')"><i class="bi bi-trash-fill"></i></button>
                    </form> --}}
                    <button type="button"
                            class="btn btn-sm me-2 btn-success open-prescription-modal"
                            data-bs-toggle="modal"
                            data-bs-target="#createPrescription"
                            data-appointment-id="{{ $appointment->id }}"
                            data-user-id="{{ $appointment->user_id }}"
                            data-patient-name="{{ $appointment->name }}"
                            title="Add Prescription">
                        <i class="bi bi-capsule"></i></button>
                    {{-- <a href="/patient_id={{$appointment->user_id}}&appointment_id={{$appointment->id}}"
                        class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2">
                    <i class="bi bi-eye"></i>
                    </a> --}}
                    <a href="{{route('appointments.show',$appointment->id)}}"
                        class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-2"
                        title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('appointment-products.create') }}?appointment_id={{ $appointment->id }}"
                        class="btn btn-sm btn-primary me-1"
                        title="Add Products">
                        <i class="bi bi-bag-plus"></i>
                    </a>
                    {{-- <button type="button" class="btn btn-sm btn-danger ">
                        <i class="bi bi-printer"></i> Print</button> --}}
                        <form method="get" action="{{route('generate-pdf',$appointment->id)}}">
                            {{-- <button type="submit">Download!</button> --}}
                            <button class="btn btn-sm  btn-danger float-end"  ><i class="bi bi-printer me-2"></i>Print</button>
                         </form>

                </div>
            </td>
        </tr>
        @endforeach
    @else
        <tr>
            <td colspan="12" class="text-center">No Data Found</td>
        </tr>
    @endif
    </tbody>
</table>
{!! $appointments->withQueryString()->onEachSide(2)->links('pagination::bootstrap-5') !!}
