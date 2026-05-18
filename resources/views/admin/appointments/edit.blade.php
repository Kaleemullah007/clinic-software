@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row pt-3 mx-1">
            <div class="col-12">
                <h4>Edit Appointment</h4>
            </div>
            <hr class="my-0">
        </div>
        <div class="shadow-css my-3">
            <div class="px-3 pb-3">
                {{-- form for for Edit appointment --}}
                @include('flash-message')

                <form method="POST" id="appointment" enctype="" action="{{ route('appointments.update', $appointment->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row">

                        <input type="hidden" value="{{$appointment->id}}" name="id" id="id">

                        <div class="col-lg-4 col-md-6 col-12 pt-3">
                            <label for="name" class="form-label fs-6">Name</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name', $appointment->name ?? '') }}"
                                autocomplete="name" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 pt-3">
                            <div class="d-flex justify-content-between">
                                <label for="phone" class="form-label fs-6 me-2">Phone</label>
                                <p class="text-danger" style="font-size: 10px">same whatsapp</p>
                            </div>
                            <div class="d-flex">
                                <input type="text"
                                    class="form-control bg-grey me-2 border-secondary @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone', $appointment->phone ?? '') }}"
                                    autocomplete="phone" required>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <input type="checkbox" id="same_whatsapp" name="same_whatsapp" class=""
                                    data-toggle="toggle" data-size="md" data-onstyle="success" data-offstyle="danger"
                                    data-onlabel="On" data-offlabel="Off" @checked(old('same_whatsapp')) checked>
                                    <input type="hidden" name="same_whatsapp_" id="same_whatsapp_" value="1" >
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 pt-3 ">
                            <label for="whatsapp_number" class="form-label fs-6">Whatsapp Number</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('whatsapp_number') is-invalid @enderror"
                                id="whatsapp_number" name="whatsapp_number"
                                value="{{ old('whatsapp_number', $appointment->whatsapp_number ?? '') }}"
                                autocomplete="whatsapp_number" required>
                            @error('whatsapp_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 pt-3">
                            <label for="email" class="form-label fs-6">Email</label>
                            <input type="email"
                                class="form-control bg-grey border-secondary @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email', $appointment->email ?? '') }}"
                                autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 pt-3">
                            <label for="date" class="form-label fs-6">Date</label>
                            <input type="date"
                                class="form-control bg-grey border-secondary @error('date') is-invalid @enderror"
                                id="date" name="date" value="{{ old('date', $appointment->date ?? '') }}"
                                autocomplete="date" required>
                            @error('date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 pt-3">
                            <label for="time" class="form-label fs-6">Time</label>
                            <input type="time"
                                class="form-control bg-grey border-secondary @error('time') is-invalid @enderror"
                                id="time" name="time" value="{{ old('time', $appointment->time ?? '') }}"
                                autocomplete="time" required>
                            @error('time')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 pt-3">
                            <label for="gender" class="form-label fs-6">Gender</label>

                            <select class="form-select bg-grey border-secondary @error('gender') is-invalid @enderror"
                                id="gender" name="gender" autocomplete="gender">

                                <option value="1" @if (old('gender') == 1) selected @endif>Female
                                </option>
                                <option value="2" @if (old('gender') == 2) selected @endif>Male
                                </option>
                                <option value="3" @if (old('gender') == 3) selected @endif>Custom
                                </option>
                            </select>
                            @error('gender')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 pt-3">
                            <label for="clinic_id" class="form-label fs-6">Clinic</label>
                            <select class="form-select bg-grey border-secondary @error('clinic_id') is-invalid @enderror"
                                id="clinic_id" name="clinic_id" autocomplete="clinic_id">
                                <option>Choose</option>
                                @foreach ($clinics as $clinic)
                                    <option value="{{ $clinic->id }}"
                                        @if ($appointment->clinic_id == $clinic->id) selected @endif>
                                        {{ $clinic->name }}
                                    </option>
                                @endforeach

                            </select>
                            @error('clinic_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 pt-3">
                            <label for="doctor_id" class="form-label fs-6">Doctor</label>
                            <select class="form-select bg-grey border-secondary @error('doctor_id') is-invalid @enderror"
                                id="doctor_id" name="doctor_id" autocomplete="doctor_id">

                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}"
                                        @if ($appointment->doctor_id == $doctor->id) selected @endif>
                                        {{ $doctor->name }}
                                    </option>
                                @endforeach

                            </select>

                                @error('doctor_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        {{--  Services  --}}


                        <div class="col-12 pt-3 setting">
                            @php

                                $counter = 0;

                            @endphp

                            {{-- If Validation Fails --}}
                            @if (old('service_id'))
                            @foreach (old('service_id') as $key => $service_old)
                            <div class="setting-row row d-flex " id="setting-row{{ $key }}">
                                <span class='totalrecord-settings'></span>
                                <div class="col-lg-4 col-md-6 col-12 pt-1">
                                    <label for="service_id" class="form-label fs-6">Service</label>
                                    <select class="form-select border-dark @error('service_id') is-invalid @enderror"
                                        name="service_id[{{ $key }}][service_id]" id="{{ $key }}-service_id"
                                        autocomplete="service_id" required onchange="getPrice({{ $key }})">
                                        <option>Choose</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}" @selected($service_old['service_id']== $service->id)>{{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_id.' . $key . '.service_id')
                                        <span class="invalid" role="alert">
                                            <strong>{{ $errors->first('service_id.' . $key . '.service_id') }} </strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 col-md-6 col-12 pt-1">
                                    <label for="price" class="form-label fs-6">Price</label>
                                    <input type="text" min="1"
                                        class="form-control calculation mb-2 border-dark @error('price') is-invalid @enderror"
                                        id="{{ $key }}-price" name="service_id[{{ $key }}][price]"
                                        placeholder="10" value="{{ old('price',$service_old['price']) }}" autocomplete="price"
                                        onkeyup="calcualtePrice()" min="0">

                                        @error('service_id.' . $key . '.price')
                                        <span class="invalid" role="alert">
                                            <strong>{{ $errors->first('service_id.' . $key . '.price') }} </strong>
                                        </span>
                                        @enderror
                                </div>


                                @if ($loop->last)
                                <div class="col-lg-2 col-md-6 col-12 d-flex align-items-end mb-2"
                                    id="setting-row{{ $key }}-btn">
                                    <a href="#" class="btn btn-success"
                                        id="setting-row{{ $key }}-href"
                                        onclick="addSetting({{ $key }})"><i
                                            class="bi bi-plus-lg"></i></a>
                                </div>
                            @else
                                <div class="col-lg-2 col-md-6 col-12 d-flex align-items-end mb-2"
                                    id="setting-row{{ $key }}-btn">
                                    <a href="#" class="btn btn-danger"
                                        id="setting-row{{ $key }}-href"
                                        rel="setting-row{{ $key }}"
                                        onclick="removeSetting({{ $key }})"><i
                                            class="bi bi-trash"></i></a>
                                </div>
                            @endif


                                {{-- <div class="col-lg-2 col-md-6 col-12 d-flex align-items-end mb-2"
                                    id="setting-row{{ $key }}-btn">
                                    <a href="#" class="btn btn-success" id="setting-row{{ $key }}-href"
                                        onclick="addSetting({{ $key }})"><i class="bi bi-plus-lg"></i></a>
                                </div> --}}
                            </div>




                            @endforeach
                            @else
                            @foreach ($appointment->appointmentService as $appointmentservice )
                            <div class="setting-row row d-flex " id="setting-row{{ $counter }}">
                                <span class='totalrecord-settings'></span>
                                <div class="col-lg-4 col-md-6 col-12 pt-1">
                                    <label for="service_id" class="form-label fs-6">Service</label>
                                    <select class="form-select border-dark @error('service_id') is-invalid @enderror"
                                        name="service_id[{{ $counter }}][service_id]" id="{{ $counter }}-service_id"
                                        autocomplete="service_id" required onchange="getPrice({{ $counter }})">
                                        <option>Choose</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}"
                                                @if (old('service_id',$appointmentservice->service_id) == $service->id) selected @endif>{{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 col-md-6 col-12 pt-1">
                                    <label for="price" class="form-label fs-6">Price</label>
                                    <input type="text" min="1"
                                        class="form-control calculation mb-2 border-dark @error('price') is-invalid @enderror"
                                        id="{{ $counter }}-price" name="service_id[{{ $counter }}][price]"
                                        placeholder="10" value="{{ old('price',$appointmentservice->discounted_price) }}" autocomplete="price"
                                        onkeyup="calcualtePrice()" min="0">
                                    @error('price')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                @if ($loop->last)
                                <div class="col-lg-2 col-md-6 col-12 d-flex align-items-end mb-2"
                                    id="setting-row{{ $counter }}-btn">
                                    <a href="#" class="btn btn-success" id="setting-row{{ $counter }}-href"
                                        onclick="addSetting({{ $counter }})"><i class="bi bi-plus-lg"></i></a>
                                </div>
                                @else
                                                <div class="col-lg-2 col-md-6 col-12 d-flex align-items-end mb-2"
                                                    id="setting-row{{ $counter }}-btn">
                                                    <a href="#" class="btn btn-danger"
                                                        id="setting-row{{ $counter }}-href"
                                                        rel="setting-row{{ $counter }}"
                                                        onclick="removeSetting(this.rel)" ><i
                                                            class="bi bi-trash"></i></a>
                                                </div>
                                @endif
                            </div>
                            @php
                            $counter++
                            @endphp
                        @endforeach

                                @endif


                        </div>
                        {{--  End of Services --}}
                        <div class="col-lg-4 col-md-6 col-12 pt-3">
                            <label for="appointment_status" class="form-label fs-6">Appointment Status</label>
                            <select
                                class="form-select bg-grey border-secondary"
                                id="appointment_status" name="appointment_status" autocomplete="appointment_status">
                                <option value="5" @if (old('appointment_status',$appointment->appointment_status) == 5) selected @endif>Approved
                                </option>
                                <option value="1" @if (old('appointment_status',$appointment->appointment_status) == 1) selected @endif>Pending
                                </option>
                                <option value="2" @if (old('appointment_status',$appointment->appointment_status) == 2) selected @endif>Accepted
                                </option>
                                <option value="3" @if (old('appointment_status',$appointment->appointment_status) == 3) selected @endif>Rejected
                                </option>
                                <option value="4" @if (old('appointment_status',$appointment->appointment_status) == 3) selected @endif>Completed
                                </option>
                            </select>
                            @error('appointment_status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 pt-3">
                            <label for="discount" class="form-label fs-6">Discount</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('discount') is-invalid @enderror"
                                id="discount" name="discount" value="{{ old('discount', $appointment->discount ?? '') }}"
                                autocomplete="discount" required>
                            @error('discount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-1 col-md-6 col-12 pt-3">
                            <label for="is_paid_" class="form-label fs-6">Is Paid</label><br>
                            <input type="checkbox" id="is_paid_" name="is_paid_" class="" data-width="100"
                                data-toggle="toggle" data-size="md" data-onstyle="success" data-offstyle="danger"
                                data-onlabel="Paid" data-offlabel="Unpaid" @checked(old('is_paid_')) checked>
                                <input type="hidden" name="is_paid" value="1" id="is_paid" >
                            @error('is_paid_')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-lg-3 col-md-6 col-12 pt-3" id="payments_">
                            <label for="discount" class="form-label fs-6">Payment Amount</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('paid_amount') is-invalid @enderror"
                                id="paid_amount" name="paid_amount" placeholder="Paid amount" value="{{ old('paid_amount',$appointment->paid_amount) }}"
                                autocomplete="paid_amount" >
                                 <input type="hidden" name="is_paid" value="1" id="is_paid" >
                            @error('paid_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                    </div>

                    <div class="row justify-content-end mt-4">
                        <div class="col-lg-4 col-md-6 col-12">
                            <table class="table table-striped border table-sm border-secondary">
                                <tbody>
                                    <tr>
                                        <th class="col-4">Sub-Total</th>
                                        <td class="col-4 text-end" id="sub_total">Rs. 0</td>
                                    </tr>
                                    <tr>
                                        <th class="col-4">Discount</th>
                                        <td class="col-4 text-end" id="show_discount">Rs. 0</td>
                                    </tr>
                                    <tr>
                                        <th class="col-4">Total</th>
                                        <td class="col-4 text-end" id="show_total">Rs. 0</td>
                                    </tr>
                                    <tr>
                                        <th class="col-4">Paid</th>
                                        <td class="col-4 text-end" id="paid">Rs. 0</td>
                                    </tr>
                                    <tr>
                                        <th class="col-4">Remaining</th>
                                        <td class="col-4 text-end" id="remaining">Rs. 0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <hr class=" border-secondary ">
                        <div class="col-12 pb-3">
                            <button type="submit" class="btn btn-success btn-md text-white"><i
                                    class="bi bi-save me-2"></i> Save</button>
                            <button type="button" class="btn btn-secondary btn-md ms-2"><i
                                    class="bi bi-x-circle me-2"></i> Cancel</button>
                        </div>
                    </div>

                </form>
                {{-- end form for Edit appointment --}}
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        calcualtePrice();
    });

</script>
@endsection

