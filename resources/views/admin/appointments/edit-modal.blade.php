<div class="modal fade" id="editAppointment" tabindex="-1" aria-labelledby="editAppointmentLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editAppointmentLabel">Edit Appointment</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- form for for Edit appointment --}}
            @include('flash-message')

            <form method="POST" id="updateForm" enctype="">

                <div class="modal-body">

                    @csrf
                    @method('PUT')
                    <input type="hidden" value="0" name="id" id="id">

                    <div class="pb-3">
                        <label for="clinic" class="form-label fs-6">Clinic</label>
                        <select
                            class="form-select bg-grey border-secondary @error('clinic') is-invalid @enderror"
                            id="clinic" name="clinic" autocomplete="clinic">
                            <option value="1" @if (old('clinic') == 1) 'selected' @endif>clinic 321
                            </option>
                            <option value="2" @if (old('clinic') == 2) 'selected' @endif>clinic 321
                            </option>
                            <option value="3" @if (old('clinic') == 3) 'selected' @endif>clinic 321
                            </option>
                            <option value="4" @if (old('clinic') == 4) 'selected' @endif>clinic 321
                            </option>
                        </select>
                        @error('clinic')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="pb-3">
                        <label for="doctor" class="form-label fs-6">Doctor</label>
                        <select
                            class="form-select bg-grey border-secondary @error('doctor') is-invalid @enderror"
                            id="doctor" name="doctor" autocomplete="doctor">
                            <option value="1" @if (old('doctor') == 1) 'selected' @endif>Doctor 786
                            </option>
                            <option value="2" @if (old('doctor') == 2) 'selected' @endif>Doctor 786
                            </option>
                            <option value="3" @if (old('doctor') == 3) 'selected' @endif>Doctor 786
                            </option>
                            <option value="4" @if (old('doctor') == 4) 'selected' @endif>Doctor 786
                            </option>
                        </select>
                        @error('doctor')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="pb-3">
                        <label for="name" class="form-label fs-6">Name</label>
                        <input type="text"
                            class="form-control bg-grey border-secondary @error('name') is-invalid @enderror"
                            id="name" name="name" value="{{ old('name', $appointment->name??'') }}"
                            autocomplete="name" required>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="pb-3">
                        <label for="email" class="form-label fs-6">Email</label>
                        <input type="email"
                            class="form-control bg-grey border-secondary @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email', $appointment->email??'') }}"
                            autocomplete="email" required>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="pb-3">
                        <label for="phone" class="form-label fs-6">Phone</label>
                        <input type="text"
                            class="form-control bg-grey border-secondary @error('phone') is-invalid @enderror"
                            id="phone" name="phone" value="{{ old('phone', $appointment->phone??'') }}"
                            autocomplete="phone" required>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="pb-3">
                        <label for="date" class="form-label fs-6">Date</label>
                        <input type="date"
                            class="form-control bg-grey border-secondary @error('date') is-invalid @enderror"
                            id="date" name="date" value="{{ old('date', $appointment->date??'') }}" autocomplete="date" required>
                        @error('date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="pb-3">
                        <label for="time" class="form-label fs-6">Time</label>
                        <input type="time"
                            class="form-control bg-grey border-secondary @error('time') is-invalid @enderror"
                            id="time" name="time" value="{{ old('time', $appointment->time??'') }}" autocomplete="time" required>
                        @error('time')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="pb-3">
                        <label for="appointment_status" class="form-label fs-6">Appointment Status</label>
                        <select
                            class="form-select bg-grey border-secondary @error('appointment_status') is-invalid @enderror"
                            id="appointment_status" name="appointment_status" autocomplete="appointment_status">
                            <option value="1" @if (old('appointment_status') == 1) 'selected' @endif>Pending
                            </option>
                            <option value="2" @if (old('appointment_status') == 2) 'selected' @endif>Accepted
                            </option>
                            <option value="3" @if (old('appointment_status') == 3) 'selected' @endif>Rejected
                            </option>
                            <option value="4" @if (old('appointment_status') == 3) 'selected' @endif>Completed
                            </option>
                        </select>
                        @error('appointment_status')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="pb-3">
                        <label for="is_paid" class="form-label fs-6">Is Paid</label><br>
                        <input type="checkbox" id="is_paid" name="is_paid" class="" data-width="100"
                            data-toggle="switchbutton" data-size="md" data-onstyle="success" data-offstyle="danger"
                            data-onlabel="Paid" data-offlabel="Unpaid" >
                            {{-- @if ($appointment->is_paid == 1) checked @endif --}}
                        @error('is_paid')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-md ms-2" data-bs-dismiss="modal"><i
                            class="bi bi-x-circle me-2"></i>Cancel</button>
                    <button type="submit" class="btn btn-success btn-md text-white"><i
                            class="bi bi-save me-2"></i>Save</button>
                </div>
            </form>
            {{-- end form for Edit appointment --}}
        </div>
    </div>
</div>
