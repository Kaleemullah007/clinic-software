@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-bag-plus me-2 text-theme-color"></i>Appointment Products</h4>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="apptProductsTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Appointment</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Doctor Share</th>
                            <th>Added By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<style>
    .text-theme-color{color:#B1083C;}
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
    #apptProductsTable thead th{background:#B1083C!important;color:#fff!important;border-color:#9a072f!important;white-space:nowrap;}
    #apptProductsTable thead .sorting_asc,#apptProductsTable thead .sorting_desc{background:#8e0630!important;}
</style>
<script>
$(function(){
    $('#apptProductsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("appointment-products.index") }}',
        columns: [
            {data:'DT_RowIndex',      name:'DT_RowIndex',       orderable:false, searchable:false, width:'50px'},
            {data:'patient_name',     name:'appointment.patient.name'},
            {data:'appointment_no',   name:'appointment.appointment_id'},
            {data:'product_col',      name:'product_name'},
            {data:'qty_fmt',          name:'quantity',           orderable:true,  searchable:false},
            {data:'unit_price_fmt',   name:'unit_price',         orderable:true,  searchable:false},
            {data:'total_price_fmt',  name:'total_price',        orderable:true,  searchable:false},
            {data:'doctor_share_fmt', name:'doctor_share_amount',orderable:true,  searchable:false},
            {data:'added_by_name',    name:'addedBy.name'},
            {data:'date_fmt',         name:'created_at',         orderable:true,  searchable:false},
            {data:'action',           name:'action',             orderable:false, searchable:false, className:'text-center'},
        ],
        order: [[9, 'desc']],
        responsive: true,
        pageLength: 15,
        language: {
            searchPlaceholder: 'Search...',
            processing: '<div class="spinner-border spinner-border-sm" style="color:#B1083C"></div> Loading…'
        },
    });
});
</script>
@endsection
