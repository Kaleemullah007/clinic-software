@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-person-badge me-2 text-theme-color"></i>Doctor Performance</h4>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Reports</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mb-3">
        <div class="col-lg-8">
            <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
                <select name="year" class="form-select border-secondary form-select-sm" style="width:100px">
                    @for($y=now()->year;$y>=now()->year-2;$y--)
                    <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
                <select name="month" class="form-select border-secondary form-select-sm" style="width:130px">
                    @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ $month==$m?'selected':'' }}>{{ \Carbon\Carbon::create(null,$m)->format('F') }}</option>
                    @endfor
                </select>
                @if(auth()->user()->isSuperAdmin())
                <select name="clinic_id" class="form-select border-secondary form-select-sm" style="width:140px">
                    <option value="">All Clinics</option>
                    @foreach($clinics as $c)
                    <option value="{{ $c->id }}" {{ ($clinicId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                <select name="doctor_id" class="form-select border-secondary form-select-sm" style="width:140px">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $d)
                    <option value="{{ $d->id }}" {{ ($doctorId ?? '') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
                @endif
                <button class="btn btn-theme btn-sm">Filter</button>
            </form>
        </div>
    </div>
    <div class="row mx-1 mb-4">
        <div class="col-lg-7">
            <div class="shadow-css p-3"><canvas id="docChart" height="150"></canvas></div>
        </div>
    </div>
    <div class="row mx-1">
        <div class="col-12">
            <div class="shadow-css p-3">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr><th>Doctor</th><th>Items</th><th>Revenue</th><th>Earnings</th></tr>
                    </thead>
                    <tbody>
                        @foreach($doctors as $doc)
                        @php $p = $performance[$doc->id] ?? null; @endphp
                        <tr>
                            <td>{{ $doc->name }}</td>
                            <td>{{ $p?->items_count ?? 0 }}</td>
                            <td>PKR {{ number_format($p?->revenue ?? 0,2) }}</td>
                            <td class="fw-bold text-theme-color">PKR {{ number_format($p?->doctor_earnings ?? 0,2) }}</td>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const docNames  = @json($doctors->pluck('name'));
const revenues  = @json($doctors->map(fn($d)=>$performance[$d->id]?->revenue??0));
const earnings  = @json($doctors->map(fn($d)=>$performance[$d->id]?->doctor_earnings??0));

new Chart(document.getElementById('docChart'),{
    type:'bar',
    data:{labels:docNames,datasets:[
        {label:'Revenue',data:revenues,backgroundColor:'rgba(177,8,60,0.7)',borderRadius:4},
        {label:'Doctor Earnings',data:earnings,backgroundColor:'rgba(54,162,235,0.6)',borderRadius:4},
    ]},
    options:{responsive:true,scales:{y:{beginAtZero:true,ticks:{callback:v=>'PKR '+v.toLocaleString()}}}}
});
</script>
@endsection
