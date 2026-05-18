@extends('layouts.admin')

@section('content')

<!-- main-content start -->
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12">
            <h3>{{__('en.Dashboard')}}</h3>
        </div>
        <hr class="my-0">
    </div>
</div>

<section>
    <div class="sm-chart-sec my-4">
        <div class="container-fluid">
            <div class="row">
                {{-- <div class="col-12 text-center py-2">
                    <h4 class="fw-bold">{{__('en.Multi Schools')}}</h4>
                </div>
                <div class="col-12 my-2">
                    <a href="#" class="w-100 text-white">
                        <div class="revinue page-one_hybrid">
                            <div class="revinue-hedding">
                                <div class="w-title">
                                    <div class="w-icon pe-3">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <div class="sm-chart-text">
                                        <p class="w-value">3</p>
                                        <h5>{{__('en.Schools')}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 text-center">
                    <h4 class="fw-bold">{{__('en.School Data')}}</h4>
                </div> --}}
                <div class="col-lg-4 col-md-6 col-sm-6 my-2">
                    <a href="#" class="w-100 text-white">
                        <div class="revinue revinue-one_hybrid">
                            <div class="revinue-hedding">
                                <div class="w-title">
                                    <div class="w-icon pe-3">
                                        <i class="bi bi-bounding-box"></i>
                                    </div>
                                    <div class="sm-chart-text">
                                        <p class="w-value">2</p>
                                        <h5>{{__('en.Catagories')}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 my-2">
                    <a href="#" class="w-100 text-white">
                        <div class="revinue page-one_hybrid">
                            <div class="revinue-hedding">
                                <div class="w-title">
                                    <div class="w-icon pe-3">
                                        <i class="bi bi-grid"></i>
                                    </div>
                                    <div class="sm-chart-text">
                                        <p class="w-value">7</p>
                                        <h5>{{__('en.Pages')}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 my-2">
                    <a href="#" class="w-100 text-white">
                        <div class="revinue bounce-one_hybrid">
                            <div class="revinue-hedding">
                                <div class="w-title">
                                    <div class="w-icon pe-3">
                                        <i class="bi bi-diagram-3"></i>
                                    </div>
                                    <div class="sm-chart-text">
                                        <p class="w-value">15</p>
                                        <h5>{{__('en.Users')}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 my-2">
                    <a href="#" class="w-100 text-white">
                        <div class="revinue rv-status-one_hybrid">
                            <div class="revinue-hedding">
                                <div class="w-title">
                                    <div class="w-icon pe-3">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div class="sm-chart-text">
                                        <p class="w-value">75   </p>
                                        <h5>{{__('en.Settings')}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 my-2">
                    <a href="#" class="w-100 text-white">
                        <div class="revinue bounce-one_hybrid">
                            <div class="revinue-hedding">
                                <div class="w-title">
                                    <div class="w-icon pe-3">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div class="sm-chart-text">
                                        <p class="w-value">18</p>
                                        <h5>{{__('en.Appointments')}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 my-2">
                    <a href="#" class="w-100 text-white">
                        <div class="revinue revinue-one_hybrid">
                            <div class="revinue-hedding">
                                <div class="w-title">
                                    <div class="w-icon pe-3">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div class="sm-chart-text">
                                        <p class="w-value">6</p>
                                        <h5>{{__('en.Doctor-description')}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 my-2">
                    <a href="#" class="w-100 text-white">
                        <div class="revinue page-one_hybrid">
                            <div class="revinue-hedding">
                                <div class="w-title">
                                    <div class="w-icon pe-3">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div class="sm-chart-text">
                                        <p class="w-value">6</p>
                                        <h5>{{__('en.Appointed Slots')}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Admin and order status table -->
<section>
    <div class="all-admin my-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 col-sm-7 mt-2">
                    <div class="order-list">
                        <p class="order-ac-title">{{__('en.Notices')}}</p>
                        <div class="data-table-section table-responsive my-2">
                            <table id="order-table" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>{{__('en.S/N')}}</th>
                                        <th>{{__('en.Title')}}</th>
                                        <th>{{__('en.Description')}}</th>
                                        <th>{{__('en.From')}}</th>
                                        <th>{{__('en.Till')}}</th>
                                        <th>{{__('en.Actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody class="order-view-tb">
                                    <tr>
                                        {{-- <td><input class="form-check-input border-secondary border-2" type="checkbox" value=""
                                                id="flexCheckDefault"></td> --}}
                                        <td>1</td>
                                        <td>Final Exams</td>
                                        <td>Final Exams will be held on 22 september 2022.</td>
                                        <td>6 Days ago</td>
                                        <td>1 Day ago</td>
                                        <td>
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#view-notice"
                                                class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                            <button type="button" class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>  
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-5 mt-2">
                    <div class="admin-list">
                        <p class="admin-ac-title">{{__('en.Academic Years')}}</p><br>
                        <label for="academicYear" class="form-label">{{__('en.Set Current Academic-Year')}}</label>
                        <select class="form-select bg-grey border-secondary @error('academicYear') is-invalid @enderror" id="academicYear" name="academicYear" autocomplete="academicYear">
                            <option value="1" @if(old('academicYear') == 1) 'selected' @endif>2021-2022</option>
                            <option value="2" @if(old('academicYear') == 2) 'selected' @endif>2022-2023</option>
                        </select>
                        <div class="d-flex justify-content-center py-4">
                            <button class="btn btn-primary w-50">{{__('en.Set Academic Year')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- main-content end -->

@endsection