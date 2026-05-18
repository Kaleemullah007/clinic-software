@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row pt-3 mx-1">
            <div class="col-12">
                <h4>Create Placeholder</h4>
            </div>
            <hr class="my-0">
        </div>
        <div class="shadow-css my-3">
            <div class="px-3 pb-3">
            {{-- form for for create placeholder --}}
            @include('flash-message')

            <form method="POST" id="placeholder"  enctype="">
                @csrf
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-12 pt-3">
                        <label for="placeholder" class="form-label fs-6">Placeholder</label>
                        <input type="placeholder"
                            class="form-control bg-grey border-secondary @error('placeholder') is-invalid @enderror"
                            id="placeholder" name="placeholder" value="{{ old('placeholder') }}"
                            autocomplete="placeholder" required>
                        @error('placeholder')
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
                                class="bi bi-save me-2"></i> Save</button>
                        <button type="button" class="btn btn-secondary btn-md ms-2"><i
                                class="bi bi-x-circle me-2"></i> Cancel</button>
                    </div>
                </div>
            </form>
            {{-- end form for create placeholder --}}
            </div>
        </div>
    </div>
@endsection


{{-- <script>

    function getPlaceholder(){
        date= $("#date1").val();
        axios.get('time-slots?date='+date)
            .then((response)=>{
                // console.log(response);
                $("#time1").html(response.data.slots);
            })
    }

    $("#placeholder").submit(function(event){
        event.preventDefault();
        var form = $("#placeholder").serialize();
            $.ajax({
                headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{route("placeholders.store")}}',
        method: 'POST',
        // contentType:'json',
        data: form,
        success: function(response) {

            $("#createPlaceholder").modal('hide');
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                jQuery.each(errors, function (key, value) {
                //   jQuery("." + key).html(value).attr('style', "color:red;");
                $("#"+ key).addClass("is-invalid");
                });

            }
        }
        });

        });

        // function addPlaceholder(){
        //     var form = $("#placeholder").serialize();
        //     console.log(form);
        //     axios.post('{{route('placeholders.store')}}')
        //          .then((response)=>{
        //             // console.log(response);
        //             $("#time1").html(response.data.slots);
        //          })
        // }

</script> --}}