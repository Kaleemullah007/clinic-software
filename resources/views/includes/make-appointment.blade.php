

<section>
    <style>
        .invalid-feedback {
            color: white !important;
        }
    </style>
    <div class="container-fluid my-5 fadeInn">
        <div class="px-3">
            <div class=" row">
                <div class="col py-5 px-3 text-theme bg-ltheme">
                    <h1 class="fw-bold py-5">We Are A Certified and Award Winning Skin Clinic You Can Trust. So, Fix an Appointment Right Now</h1>
                    <h2>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Tempore, doloribus quisquam. Perspiciatis veniam voluptates repudiandae nulla est. At, praesentium amet perspiciatis, eaque necessitatibus error consectetur nihil, beatae enim architecto dolorem. At, praesentium amet perspiciatis, eaque necessitatibus error consectetur nihil, beatae enim architecto dolorem.</h2>
                </div>
                <div class="col py-5 bg-theme text-light" id="MakeAppointment">
                    <h1 class="fw-bold text-center">Make Appointment</h1>
                    <!-- start form for Make Appointment -->
                    @if(session()->has('message'))
                    <br>
                    <div class="alert alert-secondary text-center bg-theme text-white" >
                        {{ session()->get('message') }}
                    </div>
                    @endif
                    <form method="POST" action="{{route('contactus')}}" enctype="">
                        @csrf
                        <input type="hidden" name="cp_form_name" value="appointment" >
                        <div class="row px-2 justify-content-center">
                            <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                                <select class="form-select bg-ltheme border-dark select2 @error('service') is-invalid @enderror" id="service" name="service" autocomplete="service" required>
                                    @foreach (config('mubashir.services') as $service)

                                    <option value="{{$service}}" @if (old('service')==$service) 'selected' @endif>{{$service}}
                                    </option>
                                    @endforeach

                                </select>
                                 @error('service')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                                <select class="form-select bg-ltheme border-dark select2 @error('clinic') is-invalid @enderror" id="clinic" name="clinic" autocomplete="clinic" required>
                                    {{-- @foreach (config('mubashir.clinics') as $clinic)
                                        <option value="{{$clinic}}" @if (old('clinic')==$clinic) 'selected' @endif>{{$clinic}}
                                        </option>
                                    @endforeach --}}
                                    <option value="1" @if (old('clinic') == 1) 'selected' @endif>Select clinic
                                    </option>
                                    <option value="2" @if (old('clinic') == 2) 'selected' @endif>clinic 123
                                    </option>
                                    <option value="3" @if (old('clinic') == 3) 'selected' @endif>clinic 123
                                    </option>
                                </select>
                                 @error('clinic')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                                <select class="form-select bg-ltheme border-dark select2 @error('doctor') is-invalid @enderror" id="doctor" name="doctor" autocomplete="doctor" required>
                                    {{-- @foreach (config('mubashir.doctors') as $doctor)
                                        <option value="{{$doctor}}" @if (old('doctor')==$doctor) 'selected' @endif>{{$doctor}}
                                        </option>
                                    @endforeach --}}
                                    <option value="1" @if (old('doctor') == 1) 'selected' @endif>Select doctor
                                    </option>
                                    <option value="2" @if (old('doctor') == 2) 'selected' @endif>doctor 123
                                    </option>
                                    <option value="3" @if (old('doctor') == 3) 'selected' @endif>doctor 123
                                    </option>
                                </select>
                                 @error('doctor')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                                <input type="text" class="form-control bg-ltheme border-dark @error('name') is-invalid @enderror" id="name" name="name" placeholder="Your Name" value="{{ old('name') }}" autocomplete="name" required>
                                @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                                <input type="text" class="form-control bg-ltheme border-dark @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Phone" value="{{ old('phone') }}" autocomplete="phone" required>
                                @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                                <input type="text" class="form-control bg-ltheme border-dark @error('email') is-invalid @enderror" id="email" name="email" placeholder="Your Email" value="{{ old('email') }}" autocomplete="email" required>
                                @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                @enderror
                            </div>

                            <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                                <input type="date" onchange="getAppointments()" class="form-control bg-ltheme border-dark @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date',date('Y-m-d')) }}" autocomplete="date" required>
                                @error('date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                                {{-- <input type="time" class="form-control bg-ltheme border-dark @error('time') is-invalid @enderror" id="time" name="time" value="{{ old('time') }}" autocomplete="time" required> --}}
                                <select name="time" id="time" class="form-control select2 bg-ltheme border-dark" required>

                                </select>
                                @error('time')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                @enderror
                            </div>
                            <div class="col-12 mb-3 mt-5 d-flex justify-content-center">
                                <img src="{{route('image')}}" alt="Random Code" class="text-center w-100 rounded">
                            </div>

                            <div class="col-12 mb-3 mt-4 d-flex justify-content-center input-group-lg">
                                <input type="text" name="txtSpamCode"  value="{{old('txtSpamCode')}}" placeholder="Enter above code" id="txtSpamCode" value="" maxlength="5" size="15" autocomplete="off" rel="" class="form-control bg-ltheme border-dark @error('txtSpamCode') is-invalid @enderror" />
                                @error('txtSpamCode')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class=" col-12 pt-4 text-center">
                                <button type="submit" class="btn btn-lg btn-orange fs-5 fw-bold">Make Appointment</button>
                            </div>
                        </div>
                    </form>
                    <!-- end form for Make Appointment -->
                </div>
            </div>

        </div>
    </div>
</section>
<script>

            function getAppointments(){
                date= $("#date").val()
                axios.get('time-slots?date='+date)
                     .then((response)=>{
                        // console.log(response);
                        $("#time").html(response.data.slots);
                     })
            }


     </script>
