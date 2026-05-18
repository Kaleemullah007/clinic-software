@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-wallet2 me-2 text-theme-color"></i>Salary Report — {{ $year }}</h4>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Reports</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mb-3">
        <div class="col-lg-3">
            <form method="GET" class="d-flex gap-2">
                <select name="year" class="form-select border-secondary form-select-sm">
                    @for($y=now()->year;$y>=now()->year-3;$y--)
                    <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button class="btn btn-theme btn-sm">Filter</button>
            </form>
        </div>
    </div>
    <div class="row mx-1 mb-4">
        <div class="col-lg-8">
            <div class="shadow-css p-3"><canvas id="salChart" height="120"></canvas></div>
        </div>
    </div>
    @foreach($data as $month => $salaries)
    <div class="row mx-1 mb-3">
        <div class="col-12">
            <div class="shadow-css p-3">
                <h6 class="fw-bold mb-2">{{ \Carbon\Carbon::create($year,$month)->format('F Y') }} — Total: <span class="text-theme-color">PKR {{ number_format($monthlyTotals[$month]->total??0,2) }}</span></h6>
                <table class="table table-sm table-hover">
                    <thead class="table-light"><tr><th>Employee</th><th>Basic</th><th>Bonus</th><th>Deductions</th><th>Net</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($salaries as $s)
                        <tr>
                            <td>{{ $s->user->name??'—' }}</td>
                            <td>PKR {{ number_format($s->basic_salary,2) }}</td>
                            <td>{{ $s->bonus?'PKR '.number_format($s->bonus,2):'—' }}</td>
                            <td>{{ $s->deductions?'PKR '.number_format($s->deductions,2):'—' }}</td>
                            <td class="fw-bold">PKR {{ number_format($s->net_salary,2) }}</td>
                            <td><span class="badge {{ $s->status==='paid'?'bg-success':'bg-warning text-dark' }}">{{ ucfirst($s->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
@section('script')
<style>.text-theme-color{color:#B1083C;}.btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}.shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@php
    $mLabelArr = [];
    $mTotalArr = [];
    for ($m = 1; $m <= 12; $m++) {
        $mLabelArr[] = \Carbon\Carbon::create(null, $m)->format('M');
        $mTotalArr[] = $monthlyTotals[$m]->total ?? 0;
    }
@endphp
<script>
const mLabels = @json($mLabelArr);
const mTotals = @json($mTotalArr);
new Chart(document.getElementById('salChart'),{type:'bar',data:{labels:mLabels,datasets:[{label:'Monthly Salary Total',data:mTotals,backgroundColor:'rgba(177,8,60,0.7)',borderRadius:4}]},options:{responsive:true,scales:{y:{beginAtZero:true,ticks:{callback:v=>'PKR '+v.toLocaleString()}}}}});
</script>
@endsection
