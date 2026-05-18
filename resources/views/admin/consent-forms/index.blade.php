@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-file-earmark-check me-2 text-theme-color"></i>Consent Forms</h4>
            @can('consent-forms.create')
            <a href="{{ route('consent-forms.create') }}" class="btn btn-theme btn-sm"><i class="bi bi-plus-lg me-1"></i> New Form</a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="cfTable" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr><th>#</th><th>Title</th><th>Patient</th><th>Appointment</th><th>Signed</th><th>Signed At</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach($forms as $i => $f)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $f->form_title }}</td>
                            <td>{{ $f->patient->name ?? '—' }}</td>
                            <td>{{ $f->appointment->appointment_id ?? $f->appointment_id }}</td>
                            <td>
                                <span class="badge {{ $f->signed ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $f->signed ? 'Signed' : 'Pending' }}
                                </span>
                            </td>
                            <td>{{ $f->signed_at ? $f->signed_at->format('d M Y') : '—' }}</td>
                            <td class="d-flex gap-1">
                                @if(!$f->signed)
                                <a href="{{ route('consent-form.sign', $f) }}" class="btn btn-sm btn-success" title="Get Signature">
                                    <i class="bi bi-pen"></i>
                                </a>
                                @endif
                                @can('consent-forms.edit')
                                <a href="{{ route('consent-forms.edit', $f) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('consent-forms.delete')
                                <form action="{{ route('consent-forms.destroy',$f) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
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
<script>$(function(){ $('#cfTable').DataTable({responsive:true,pageLength:25}); });</script>
@endsection
