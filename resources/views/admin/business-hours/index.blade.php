@extends('layouts.admin')

@section('content')
    <!-- main-content start -->
    <div class="container-fluid">
        <div class="container">
            <div class="row pt-3">
                <div class="col-12">
                    <h4>Business Hours</h4>
                </div>
                <hr>
            </div>
            @include('flash-message')
            
            <form action="{{route('businesshour.store')}}" method="post">
                @csrf
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
                <div class="table-responsive">
                    <table class="table table-bordered ">
                        <thead>
                            <tr>
                                <th class="col-2">On / Off</th>
                                <th class="col-2">Days</th>
                                <th class="col-3">From</th>
                                <th class="col-3">To</th>
                                <th class="col-2">Step</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // $days = array('Monday','Tuesday','Wed','Thurs','Fri','Sat','Sun');
                            @endphp
                            @foreach ($days as $key => $day)

                            <tr>
                                <th>
                                    <input type="checkbox" name="data[{{$day->day}}][is_day]" data-toggle="switchbutton" @checked($day->is_day ??0) data-size="sm"
                                        data-onstyle="success" data-offstyle="danger" data-onlabel="ON" data-offlabel="OFF">
                                </th>
                                <th>{{$day->day}}
                                    <input type="hidden"
                                    class="form-control bg-grey border-secondary @error('day') is-invalid @enderror"
                                    id="day" name="data[{{$day->day}}][day]" value="{{ old('day',$day->day) }}"
                                    >
                                </th>
                                <td>
                                    <div class="">
                                        <input type="time"
                                            class="form-control bg-grey border-secondary @error('startTime') is-invalid @enderror"
                                            id="startTime{{$day->day}}" name="data[{{$day->day}}][from]" value="{{ old('from',date('h:i',strtotime($day->from))) }}"
                                            >

                                            <?php
                                            // echo "<pre>";
                                            //echo  $day->day; print_r( $errors); ?>
                                            {{ $errors->first('data.' . $day->day . '.from') }}
                                        {{-- @error('data.'.$day->day.'.from') --}}
                                        <span class="invalid-feedback" role="alert">
                                            {{-- {{ $errors->has("data.".$day->day.".from") }} --}}
                                            <strong>{{ $errors->first('data.' . $day->day . '.from') }} </strong>
                                        </span>
                                        {{-- @enderror --}}
                                    </div>
                                </td>
                                <td>
                                    <div class="">
                                        <input type="time"
                                            class="form-control bg-grey border-secondary @error('stopTime') is-invalid @enderror"
                                            id="stopTime{{$day->day}}" name="data[{{$day->day}}][to]" value="{{ old('to',date('h:i',strtotime($day->to))) }}"
                                            >
                                        @error('stopTime')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <div class="">
                                        <input type="number"
                                            class="form-control bg-grey border-secondary @error('step') is-invalid @enderror"
                                            id="step" name="data[{{$day->day}}][step]"value="{{ old('step',$day->step) }}"
                                            autocomplete="step" >
                                        @error('step')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        </div>

    </div>
@endsection
