@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-telephone me-2 text-theme-color"></i>Call Logs</h4>
            @can('call-logs.create')
            <a href="{{ route('call-logs.create') }}" class="btn btn-theme btn-sm"><i class="bi bi-plus-lg me-1"></i> Log Call</a>
            @endcan
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1">
        <div class="col-12">
            @include('flash-message')
            <div class="shadow-css p-3">
                <table id="callTable" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr><th>Appointment</th><th>Patient</th><th>Type</th><th>Status</th><th>Called By</th><th>Call Time</th><th>Notes</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->appointment->appointment_id ?? '—' }}</td>
                            <td>{{ $log->patient->name ?? '—' }}</td>
                            <td><span class="badge bg-info text-dark">{{ ucfirst(str_replace('_',' ',$log->call_type)) }}</span></td>
                            <td>
                                @php $scolors=['answered'=>'success','no_answer'=>'secondary','busy'=>'warning','scheduled'=>'primary']; @endphp
                                <span class="badge bg-{{ $scolors[$log->call_status]??'secondary' }}">{{ ucfirst(str_replace('_',' ',$log->call_status)) }}</span>
                            </td>
                            <td>{{ $log->calledBy->name ?? '—' }}</td>
                            <td>{{ $log->call_at ? \Carbon\Carbon::parse($log->call_at)->format('d M Y H:i') : $log->created_at->format('d M Y H:i') }}</td>
                            <td>{{ Str::limit($log->notes,40) ?? '—' }}</td>
                            <td>
                                @can('call-logs.edit')
                                <a href="{{ route('call-logs.edit',$log) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('call-logs.delete')
                                <form action="{{ route('call-logs.destroy',$log) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<style>.text-theme-color{color:#B1083C;}.btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}.shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}</style>
<script>$(function(){ $('#callTable').DataTable({responsive:true,pageLength:25,order:[[5,'desc']],paging:false}); });</script>
@endsection
