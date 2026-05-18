@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-file-earmark-text me-2 text-theme-color"></i>Doctor Agreements</h4>
            @can('doctor-agreements.create')
            <a href="{{ route('doctor-agreements.create') }}" class="btn btn-theme btn-sm"><i class="bi bi-plus-lg me-1"></i> New Agreement</a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="agTable" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr><th>#</th><th>Doctor</th><th>Clinic</th><th>Service</th><th>Type</th><th>Doctor%</th><th>Clinic%</th><th>From</th><th>To</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach($agreements as $i => $a)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $a->doctor->name ?? '—' }}</td>
                            <td>{{ $a->clinic->name ?? 'All' }}</td>
                            <td>{{ $a->service->name ?? 'All' }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($a->share_type) }}</span></td>
                            <td>{{ $a->doctor_share }}{{ $a->share_type==='percentage'?'%':' PKR' }}</td>
                            <td>{{ $a->clinic_share }}{{ $a->share_type==='percentage'?'%':' PKR' }}</td>
                            <td>{{ \Carbon\Carbon::parse($a->effective_from)->format('d M Y') }}</td>
                            <td>{{ $a->effective_to ? \Carbon\Carbon::parse($a->effective_to)->format('d M Y') : 'Ongoing' }}</td>
                            <td><span class="badge {{ $a->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $a->is_active?'Active':'Inactive' }}</span></td>
                            <td>
                                @can('doctor-agreements.edit')
                                <a href="{{ route('doctor-agreements.edit',$a) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('doctor-agreements.delete')
                                <form action="{{ route('doctor-agreements.destroy',$a) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<style>.text-theme-color{color:#B1083C;}.btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}.shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}</style>
<script>$(function(){ $('#agTable').DataTable({responsive:true,pageLength:25}); });</script>
@endsection
