@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-graph-up-arrow me-2 text-theme-color"></i>Revenue Report</h4>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Reports</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mb-3">
        <div class="col-lg-4">
            <form method="GET" class="d-flex gap-2">
                <select name="year" class="form-select border-secondary form-select-sm">
                    @for($y=now()->year;$y>=now()->year-3;$y--)
                    <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
                <select name="month" class="form-select border-secondary form-select-sm">
                    <option value="">All Months</option>
                    @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ $month==$m?'selected':'' }}>{{ \Carbon\Carbon::create(null,$m)->format('F') }}</option>
                    @endfor
                </select>
                <button class="btn btn-theme btn-sm">Filter</button>
            </form>
        </div>
        <div class="col-lg-3 ms-auto text-end">
            <div class="shadow-css p-3">
                <p class="mb-0 text-muted small">Total Revenue</p>
                <h4 class="fw-bold text-theme-color mb-0">PKR {{ number_format($totalRevenue,2) }}</h4>
            </div>
        </div>
    </div>

    <div class="row mx-1 mb-4">
        <div class="col-lg-8">
            <div class="shadow-css p-3">
                <canvas id="revenueChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <div class="row mx-1">
        <div class="col-12">
            <div class="shadow-css p-3">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr><th>Period</th><th>Revenue</th><th>Doctor Share</th><th>Clinic Share</th></tr>
                    </thead>
                    <tbody>
                        @foreach($query as $row)
                        <tr>
                            <td>{{ \Carbon\Carbon::create($row->year,$row->month)->format('F Y') }}</td>
                            <td class="fw-semibold text-theme-color">PKR {{ number_format($row->revenue,2) }}</td>
                            <td>PKR {{ number_format($row->doctor_share,2) }}</td>
                            <td>PKR {{ number_format($row->clinic_share,2) }}</td>
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
const labels = @json($query->map(fn($r)=>\Carbon\Carbon::create($r->year,$r->month)->format('M Y')));
const revenues = @json($query->pluck('revenue'));
const doctorShares = @json($query->pluck('doctor_share'));
const clinicShares = @json($query->pluck('clinic_share'));

new Chart(document.getElementById('revenueChart'),{
    type:'bar',
    data:{
        labels,
        datasets:[
            {label:'Revenue',data:revenues,backgroundColor:'rgba(177,8,60,0.7)',borderRadius:4},
            {label:'Doctor Share',data:doctorShares,backgroundColor:'rgba(54,162,235,0.6)',borderRadius:4},
            {label:'Clinic Share',data:clinicShares,backgroundColor:'rgba(75,192,192,0.6)',borderRadius:4},
        ]
    },
    options:{responsive:true,plugins:{legend:{position:'top'}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'PKR '+v.toLocaleString()}}}}
});
</script>
@endsection
