@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row pt-3 mx-1">
            <div class="col-12">
                <h4>Update Profile</h4>
            </div>
            <hr class="my-0">
        </div>
        <div class="shadow-css my-3">
            <div class="px-3 pb-3">
                {{-- form for for Edit User --}}
                @include('flash-message')
                {{-- <form method="POST" action="{{ route('users.update', [$User->id]) }}" enctype=""> --}}
                    <form method="POST" action="{{ route('users.updateprofile', [$User->id]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="name" class="form-label fs-6">Name </label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name', $User->name) }}"
                                autocomplete="name" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="email" class="form-label fs-6">Email</label>
                            <input type="email"
                                class="form-control bg-grey border-secondary @error('email') is-invalid @enderror"
                                id="email" name="email"
                                value="{{ old('email', $User->email) }}" autocomplete="email" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="password" class="form-label fs-6">Password</label>
                            <input type="password"
                                class="form-control bg-grey border-secondary @error('password') is-invalid @enderror"
                                id="password" name="password" value="{{ old('password') }}"
                                autocomplete="password" >
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="role" class="form-label fs-6">Confirm Password</label>
                            <input class="form-control bg-grey border-secondary @error('password_confirmation') is-invalid @enderror"
                                id="password_confirmation" name="password_confirmation" autocomplete="NewPassword"
                                value="{{ old('password_confirmation') }}" />


                            @error('NewPassword')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 pt-3">
                            <label for="status" class="form-label fs-6">Picture</label><br>
                            <input type="file" id="file" name="avatar" onchange="filePreview(this)">
                            <div id="">
                                <img src="{{asset('images/avatar/'.$User->avatar)}}" alt="" id="pic" class="rounded">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12 pt-3">
                            <label for="button" class="form-label fs-6">Appointment Button</label><br>
                            <input type="checkbox" id="button" name="button"
                             data-toggle="switchbutton" data-size="md" data-onstyle="success"
                                data-offstyle="danger" data-onlabel="Show" data-offlabel="Hide" checked>
                            @error('button')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 pt-3">
                            <label for="education" class="form-label fs-6">Education</label>
                            <input type="education"
                                class="form-control bg-grey border-secondary @error('education') is-invalid @enderror"
                                id="education" name="education" placeholder="Education"
                                value="{{ old('education') }}" autocomplete="education" >
                            @error('education')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div>
                            <label for="Description" class="form-label fs-6">About Description</label>
                            <textarea class="form-control bg-grey border-secondary @error('Description') is-invalid @enderror"
                                id="Description" name="Description" value="{{ old('Description') }}" style="height: 100px"
                                ></textarea>
                            @error('Description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                        <div class="row mt-4">
                            <hr class=" border-secondary ">
                            <div class="col-12 pb-3">
                                <button type="submit" class="btn btn-success btn-md text-white"><i
                                        class="bi bi-save me-2"></i> Update</button>
                                <button type="button" class="btn btn-secondary btn-md ms-2"><i
                                        class="bi bi-x-circle me-2"></i> Cancel</button>
                            </div>
                        </div>

                </form>
                {{-- end form for Edit User --}}
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    function filePreview(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $('#pic').attr('src', e.target.result)
          .height(150)
          .width(150);
        $("#pic").removeClass('hide');
        $("#placeholder").addClass('hide');
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

</script>
@endsection
