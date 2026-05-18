        <section id="MakeAppointment">
            <div class="container-fluid my-5 fadeInn">
                <div class="row p-5">
                    <div class="d-flex flex-sm-column flex-md-row flex-lg-row">
                        <div class=" p-5 text-theme bg-ltheme">
                            <h1 class="fw-bold py-5">We Are A Certified and Award Winning Skin Clinic You Can Trust. So, Fix an Appointment Right Now</h1>
                            <h4>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Tempore, doloribus quisquam. Perspiciatis veniam voluptates repudiandae nulla est. At, praesentium amet perspiciatis, eaque necessitatibus error consectetur nihil, beatae enim architecto dolorem.</h4>
                        </div>
                        <div class=" py-5 bg-theme text-light">
                            <h1 class="fw-bold text-center">Make Appointment</h1>
                            <!-- start form for Make Appointment -->
                            <form method="POST" action="" enctype="">
                                <div class="row px-5 justify-content-center">
                                    <div class="col-12 pt-5 input-group-lg">
                                        <select class="form-select bg-ltheme border-dark @error('makeAppointSer') is-invalid @enderror" id="makeAppointSer" name="makeAppointSer" autocomplete="makeAppointSer" required>
                                            <option value="1" @if (old('makeAppointSer')==1) 'selected' @endif>Select a Service
                                            </option>
                                            <option value="2" @if (old('makeAppointSer')==2) 'selected' @endif>Service 1
                                            </option>
                                            <option value="3" @if (old('makeAppointSer')==3) 'selected' @endif>Service 2
                                            </option>
                                            <option value="4" @if (old('makeAppointSer')==4) 'selected' @endif>Service 3
                                            </option>
                                        </select>
                                        <!-- @error('makeAppointSer')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                        @enderror -->
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                                        <input type="text" class="form-control bg-ltheme border-dark @error('makeAppointName') is-invalid @enderror" id="makeAppointName" name="makeAppointName" placeholder="Your Name" value="{{ old('makeAppointName') }}" autocomplete="makeAppointName" required>
                                        <!-- @error('makeAppointName')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                        @enderror -->
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                                        <input type="text" class="form-control bg-ltheme border-dark @error('makeAppointEmail') is-invalid @enderror" id="makeAppointEmail" name="makeAppointEmail" placeholder="Your Email" value="{{ old('makeAppointEmail') }}" autocomplete="makeAppointEmail" required>
                                        <!-- @error('makeAppointEmail')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                        @enderror -->
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                                        <input type="date" class="form-control bg-ltheme border-dark @error('makeAppointDate') is-invalid @enderror" id="makeAppointDate" name="makeAppointDate" placeholder="Your Name" value="{{ old('makeAppointDate') }}" autocomplete="makeAppointDate" required>
                                        <!-- @error('makeAppointDate')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                        @enderror -->
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12 pt-5 input-group-lg">
                                        <input type="time" class="form-control bg-ltheme border-dark @error('makeAppointTime') is-invalid @enderror" id="makeAppointTime" name="makeAppointTime" placeholder="Your Email" value="{{ old('makeAppointTime') }}" autocomplete="makeAppointTime" required>
                                        <!-- @error('makeAppointTime')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                        @enderror -->
                                    </div>
                                    <div class="col-12 pt-5 text-center">
                                        <button type="submit" class="btn btn-orange fs-5 fw-bold">Make Appointment</button>
                                    </div>
                                </div>
                            </form>
                            <!-- end form for Make Appointment -->
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>