@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-cash-stack me-2 text-theme-color"></i>Expense Report</h4>
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
    </div>
    <div class="row mx-1 mb-4">
        <div class="col-lg-5">
            <div class="shadow-css p-3"><canvas id="pieChart" height="220"></canvas></div>
        </div>
        <div class="col-lg-7">
            <div class="shadow-css p-3"><canvas id="monthlyChart" height="220"></canvas></div>
        </div>
    </div>
    <div class="row mx-1">
        <div class="col-md-6">
            <div class="shadow-css p-3">
                <h6 class="fw-bold mb-3">By Category</h6>
                <table class="table table-sm table-hover">
                    <thead class="table-light"><tr><th>Category</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach($data as $row)
                        <tr>
                            <td>{{ $row->category ?? 'Uncategorised' }}</td>
                            <td class="fw-semibold">PKR {{ number_format($row->total,2) }}</td>
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
@php
    $monthLabelArr = [];
    $monthValArr   = [];
    for ($m = 1; $m <= 12; $m++) {
        $monthLabelArr[] = \Carbon\Carbon::create(null, $m)->format('M');
        $monthValArr[]   = $monthly[$m]->total ?? 0;
    }
@endphp
<script>
const catLabels   = @json($data->pluck('category')->map(fn($c) => $c ?? 'Uncategorised'));
const catTotals   = @json($data->pluck('total'));
const monthLabels = @json($monthLabelArr);
const monthVals   = @json($monthValArr);

new Chart(document.getElementById('pieChart'),{type:'pie',data:{labels:catLabels,datasets:[{data:catTotals,backgroundColor:['#B1083C','#d13729','#f76c6c','#ffa07a','#ffd700','#98fb98','#87ceeb']}]},options:{responsive:true,plugins:{legend:{position:'right'}}}});
new Chart(document.getElementById('monthlyChart'),{type:'line',data:{labels:monthLabels,datasets:[{label:'Monthly Expenses',data:monthVals,borderColor:'#B1083C',backgroundColor:'rgba(177,8,60,0.1)',fill:true,tension:0.4}]},options:{responsive:true,scales:{y:{beginAtZero:true,ticks:{callback:v=>'PKR '+v.toLocaleString()}}}}});
</script>
@endsection
