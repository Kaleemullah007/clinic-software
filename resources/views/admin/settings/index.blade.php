@extends('layouts.admin')

@section('content')

<!-- main-content start -->
<div class="container-fluid">
        <div class="container">
            <div class="row pt-3">
                <div class="col-12">
                    <h4>Settings</h4>
                </div>
                <hr>
            </div>

            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 col-12 mt-2 d-flex ">
                    <label for="search" class="form-label mt-1"><i class="bi bi-search "></i></label>
                    <input type="text" class="form-control bg-grey form-control-css border-secondary ms-3 rounded"
                        placeholder="Search this table..." id="search">
                </div>
                <div class="col-lg-9 col-md-6 col-12 mt-2 text-end">
                    <!-- offcanvas trigger for filter -->
                    {{-- <button type="button" class="btn btn-sm me-2 btn-outline-primary" data-bs-toggle="offcanvas"
                        data-bs-target="#filters" aria-controls="filters"><i class="bi bi-funnel"></i> Filter</button>
                    <button type="button" class="btn btn-sm me-2 btn-outline-success"><i class="bi bi-filetype-pdf"></i>
                        PDF</button>
                    <button type="button" class="btn btn-sm me-2 btn-outline-danger"><i
                            class="bi bi-file-earmark-excel-fill"></i> EXCEL</button> --}}
                    <!-- modal trigger for create plan -->
                    {{-- <a href="{{ route('settings.create') }}" class="btn btn-sm me-2 btn-success">
                        <i class="bi bi-plus-circle me-2"></i>Create</a> --}}
                </div>
            </div>
            <div class="">
                @include('flash-message')
                <form method="POST" action="{{route('settings.store')}}" enctype="">
                    @csrf
                    @php
                        $row = 1;
                        $totalrecords = 0;
                    @endphp
                   <div class="setting">
                    @foreach ($settings as $setting)
                    <input type="hidden" name="all_ids[]" value="{{$setting->id}}" >

                    <div class="row setting-row" id="setting-row{{$row}}" >
                        <input type="hidden" name="ids[]" value="{{$setting->id}}" >
                        <div class="col-lg-3 col-md-6 col-12 pt-3">
                            <label for="key_name" class="form-label fs-6">Name</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('key_name') is-invalid @enderror"
                                id="key_name" name="data[{{$row}}][key_name]" placeholder="Name" value="{{ old('key_name',$setting->key_name) }}"
                                autocomplete="key_name" required>
                            @error('key_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 pt-3">
                            <label for="key_value" class="form-label fs-6">Value</label>
                            @if($setting->key_name === 'receipt_style')
                                <select class="form-select bg-grey border-secondary"
                                        name="data[{{$row}}][key_value]" required>
                                    <option value="v1" {{ $setting->key_value === 'v1' ? 'selected' : '' }}>V1 — Classic Table (Citi Lab style)</option>
                                    <option value="v2" {{ $setting->key_value === 'v2' ? 'selected' : '' }}>V2 — Modern Colour Header</option>
                                    <option value="v3" {{ $setting->key_value === 'v3' ? 'selected' : '' }}>V3 — Minimal Clean</option>
                                    <option value="v4" {{ $setting->key_value === 'v4' ? 'selected' : '' }}>V4 — Bold Dark Header</option>
                                </select>
                            @elseif($setting->key_name === 'default_receipt')
                                <select class="form-select bg-grey border-secondary"
                                        name="data[{{$row}}][key_value]" required>
                                    <option value="services_receipt" {{ $setting->key_value === 'services_receipt' ? 'selected' : '' }}>Services Receipt</option>
                                    <option value="products_receipt" {{ $setting->key_value === 'products_receipt' ? 'selected' : '' }}>Products Receipt</option>
                                    <option value="both"             {{ $setting->key_value === 'both'             ? 'selected' : '' }}>Both Receipts</option>
                                </select>
                            @else
                                <input type="text"
                                    class="form-control bg-grey border-secondary @error('key_value') is-invalid @enderror"
                                    id="key_value" name="data[{{$row}}][key_value]" placeholder="Value"
                                    value="{{ old('key_value',$setting->key_value) }}" autocomplete="key_value" required>
                                @error('key_value')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            @endif
                        </div>

                        <div class="col-lg-3 col-md-6 col-12 pt-3">
                            <span class='totalrecord-settings'></span>
                            <label for="status" class="form-label fs-6">Status</label><br>
                            <input type="checkbox" id="status{{$row}}" name="data[{{$row}}][status]"
                             data-toggle="switchbutton" data-size="md" data-onstyle="success"
                                data-offstyle="danger" data-onlabel="Active" data-offlabel="Inactive" @checked($setting->status)>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 pt-3 pt-5" id="setting-row{{$row}}-btn">
                            @if($loop->last)
                            <a href="#" class="btn btn-success " id="setting-row{{$row}}-href" onclick="addSetting({{$row}})"><i class="bi bi-plus-lg"></i> Add</a>
                            @else
                            <a href="#" class="btn btn-danger" rel="setting-row{{$row}}" onclick="removeSetting(this.rel)"><i class="bi bi-minus-lg"></i> Remove</a>
                            @endif
                        </div>
                    </div>

                    @php
                        $row += 1;
                        $totalrecords++;
                    @endphp

                    @endforeach


                    @if($settings->count() == 0)
                    <div class="row setting-row" id="setting-row{{$row}}" >
                        <div class="col-lg-3 col-md-6 col-12 pt-3">
                            <label for="key_name" class="form-label fs-6">Name</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('key_name') is-invalid @enderror"
                                id="key_name" name="data[{{$row}}][key_name]" placeholder="Name" value="{{ old('key_name') }}"
                                autocomplete="key_name" required>
                            @error('key_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 pt-3">
                            <label for="key_value" class="form-label fs-6">Value</label>
                            <input type="text"
                                class="form-control bg-grey border-secondary @error('key_value') is-invalid @enderror"
                                id="key_value" name="data[{{$row}}][key_value]" placeholder="Value"
                                value="{{ old('key_value') }}" autocomplete="key_value" required>
                            @error('key_value')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-lg-3 col-md-6 col-12 pt-3">
                            <span class='totalrecord-settings'></span>
                            <label for="status" class="form-label fs-6">Status</label><br>
                            <input type="checkbox" id="status{{$row}}" name="data[{{$row}}][status]"
                             data-toggle="switchbutton" data-size="md" data-onstyle="success"
                                data-offstyle="danger" data-onlabel="Active" data-offlabel="Inactive">
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 pt-3 pt-5" id="setting-row{{$row}}-btn">
                            <a href="#" class="btn btn-success " id="setting-row{{$row}}-href" onclick="addSetting({{$row}})"><i class="bi bi-plus-lg"></i> Add</a>
                        </div>
                    </div>

                    @endif


                   </div>
                    <div class="row">
                        <div class="row mt-4">
                            <hr class=" border-secondary ">
                            <div class="col-12 pb-3">
                                <button type="submit" class="btn btn-success btn-md text-white"><i
                                        class="bi bi-save me-2"></i> Save</button>
                                <button type="button" class="btn btn-secondary btn-md ms-2"><i
                                        class="bi bi-x-circle me-2"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

</div>

@endsection

@section('script')

<script>

    function addSetting(id) {
        $("#setting-row" +id+ "-href").attr('disabled',true)
       var OldRow = id;
            totalrows = $(".setting > .setting-row").length;
            totalrecord = $('.totalrecord-settings').length;
         var div = $(".setting > .setting-row:last");
         FirstRowId = div.attr('id');
         lastRow = FirstRowId.split("").reverse().join("");
        //  console.log(lastRow);
         var NextRow = parseInt(lastRow) + 1;

         $.ajaxSetup({

           headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
         });

         var addButton = '<a href="#" class="btn btn-success " onclick="addSetting('+NextRow+')"><i class="bi bi-plus-lg"></i> Add</a>';
         var removeButton = '<a href="#" class="btn btn-danger" rel='+FirstRowId+' onclick="removeSetting(this.rel)"><i class="bi bi-minus-lg"></i> Remove</a>';
         $("#"+FirstRowId+'-btn').html(removeButton);

        //  $(".setting").append("<div class='setting-row' id='setting-row"+NextRow+"' >Hello  <a href='#' class='btn btn-success ' onclick='removeSetting("+NextRow+")'><i class='bi bi-minus-lg'></i> Remove</a></div>");

         console.log(totalrows+ ' '+ NextRow);
         $.ajax({

           type: 'get',

           url: '{{ URL::to("/add-new-row") }}',

           data: { new_row: NextRow,totalrecord:totalrecord },
           dataType: 'html',

           success: function (data) {
            $("#" + FirstRowId+ "-btn").html(removeButton)

             $(".setting").append(data)
             setTimeout(() => {
                // $("[data-toggle='switchbutton']").bootstrapSwitch();
                document.getElementById('status'+NextRow).switchButton('on');
                // switchButton
             }, 500);

           }
         })


     }


       function removeSetting(id) {
           $("#"+id).remove();

       }

</script>
@endsection
